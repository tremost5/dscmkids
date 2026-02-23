<?php

namespace App\Services;

use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SchoolDataService
{
    public function buildDashboardData(): array
    {
        try {
            $connection = config('school_data.connection', 'external');
            $studentsTable = config('school_data.students.table');
            $attendanceTable = config('school_data.attendance.table');
            $galleryTable = config('school_data.gallery.table');

            if (!Schema::connection($connection)->hasTable($studentsTable) || !Schema::connection($connection)->hasTable($attendanceTable)) {
                return $this->fallbackData();
            }

            $studentTotal = $this->fetchStudentTotal($connection);
            $attendanceSeries = $this->fetchAttendanceSeries($connection, 14);
            $classAttendance = $this->fetchTodayClassAttendance($connection, $studentTotal);
            $todayRate = $attendanceSeries[count($attendanceSeries) - 1]['value'] ?? 0;
            $weeklyAverage = round(collect($attendanceSeries)->avg('value'), 1);
            $todayAttendance = (int) ($classAttendance['present_total'] ?? 0);
            $activeClasses = $this->fetchActiveClasses($connection);

            return [
                'source' => 'external',
                'metrics' => [
                    'students_total' => $studentTotal,
                    'attendance_today' => $todayAttendance,
                    'attendance_rate' => round($todayRate, 1),
                    'weekly_average' => $weeklyAverage,
                    'active_classes' => $activeClasses,
                ],
                'attendance_series' => $attendanceSeries,
                'attendance_by_class' => $classAttendance['classes'] ?? [],
                'attendance_totals' => [
                    'present' => $todayAttendance,
                    'absent' => max(0, $studentTotal - $todayAttendance),
                ],
                'gallery' => $this->fetchGallery($connection, $galleryTable),
            ];
        } catch (Throwable) {
            return $this->fallbackData();
        }
    }

    private function fetchStudentTotal(string $connection): int
    {
        $table = config('school_data.students.table');
        $activeColumn = config('school_data.students.active_column');

        $query = DB::connection($connection)->table($table);

        if ($activeColumn && Schema::connection($connection)->hasColumn($table, $activeColumn)) {
            $query->where($activeColumn, 1);
        }

        return (int) $query->count();
    }

    private function fetchActiveClasses(string $connection): int
    {
        $table = config('school_data.students.table');
        $classColumn = config('school_data.students.class_column');

        if (!$classColumn || !Schema::connection($connection)->hasColumn($table, $classColumn)) {
            return count(config('school_data.class_rollup', []));
        }

        return (int) DB::connection($connection)
            ->table($table)
            ->whereNotNull($classColumn)
            ->distinct($classColumn)
            ->count($classColumn);
    }

    private function fetchAttendanceSeries(string $connection, int $days): array
    {
        $table = config('school_data.attendance.table');
        $dateColumn = config('school_data.attendance.date_column');
        $statusColumn = config('school_data.attendance.status_column');
        $schema = Schema::connection($connection);

        if (!$schema->hasColumn($table, $dateColumn) || !$schema->hasColumn($table, $statusColumn)) {
            return $this->generateFallbackSeries();
        }

        $start = Carbon::today()->subDays($days - 1)->toDateString();
        $end = Carbon::today()->toDateString();

        $rows = DB::connection($connection)
            ->table($table)
            ->whereBetween($dateColumn, [$start, $end])
            ->get([$dateColumn, $statusColumn]);

        $grouped = [];

        foreach ($rows as $row) {
            $dateKey = Carbon::parse($row->{$dateColumn})->toDateString();

            if (!isset($grouped[$dateKey])) {
                $grouped[$dateKey] = ['present' => 0, 'total' => 0];
            }

            $grouped[$dateKey]['total']++;

            if ($this->isPresent($row->{$statusColumn})) {
                $grouped[$dateKey]['present']++;
            }
        }

        $series = [];

        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::today()->subDays($days - 1 - $i);
            $key = $date->toDateString();
            $bucket = $grouped[$key] ?? ['present' => 0, 'total' => 0];
            $value = $bucket['total'] > 0 ? ($bucket['present'] / $bucket['total']) * 100 : 0;

            $series[] = [
                'label' => $date->translatedFormat('d M'),
                'value' => round($value, 1),
            ];
        }

        return $series;
    }

    private function fetchTodayClassAttendance(string $connection, int $studentTotal): array
    {
        $classRollup = collect(config('school_data.class_rollup', []))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values();

        $table = config('school_data.attendance.table');
        $dateColumn = config('school_data.attendance.date_column');
        $statusColumn = config('school_data.attendance.status_column');
        $attendanceClassColumn = config('school_data.attendance.class_column');
        $attendanceStudentColumn = config('school_data.attendance.student_id_column');

        $studentsTable = config('school_data.students.table');
        $studentIdColumn = 'id';
        $studentClassColumn = config('school_data.students.class_column');
        $schema = Schema::connection($connection);

        if (!$schema->hasColumn($table, $dateColumn) || !$schema->hasColumn($table, $statusColumn)) {
            return $this->fallbackClassAttendance($studentTotal);
        }

        $today = Carbon::today()->toDateString();

        $query = DB::connection($connection)
            ->table($table)
            ->whereDate($dateColumn, $today)
            ->whereIn($statusColumn, config('school_data.attendance.present_values', []));

        if ($attendanceClassColumn && $schema->hasColumn($table, $attendanceClassColumn)) {
            $rows = $query->select([$attendanceClassColumn, DB::raw('COUNT(*) as total')])
                ->groupBy($attendanceClassColumn)
                ->get();

            $counts = [];
            foreach ($rows as $row) {
                $key = strtoupper(trim((string) $row->{$attendanceClassColumn}));
                $counts[$key] = (int) $row->total;
            }
        } elseif (
            $attendanceStudentColumn
            && $schema->hasColumn($table, $attendanceStudentColumn)
            && Schema::connection($connection)->hasColumn($studentsTable, $studentIdColumn)
            && Schema::connection($connection)->hasColumn($studentsTable, $studentClassColumn)
        ) {
            $rows = $query
                ->join($studentsTable, $studentsTable.'.'.$studentIdColumn, '=', $table.'.'.$attendanceStudentColumn)
                ->select([$studentsTable.'.'.$studentClassColumn.' as class_name', DB::raw('COUNT(*) as total')])
                ->groupBy($studentsTable.'.'.$studentClassColumn)
                ->get();

            $counts = [];
            foreach ($rows as $row) {
                $key = strtoupper(trim((string) $row->class_name));
                $counts[$key] = (int) $row->total;
            }
        } else {
            return $this->fallbackClassAttendance($studentTotal);
        }

        $classes = [];
        $presentTotal = 0;

        foreach ($classRollup as $className) {
            $normalized = strtoupper(trim($className));
            $value = (int) ($counts[$normalized] ?? 0);
            $classes[] = ['class' => $className, 'present' => $value];
            $presentTotal += $value;
        }

        if (empty($classes)) {
            return $this->fallbackClassAttendance($studentTotal);
        }

        return [
            'classes' => $classes,
            'present_total' => $presentTotal,
        ];
    }

    private function fetchGallery(string $connection, string $table): array
    {
        if (!Schema::connection($connection)->hasTable($table)) {
            return $this->localGallery();
        }

        $titleColumn = config('school_data.gallery.title_column');
        $pathColumn = config('school_data.gallery.path_column');
        $dateColumn = config('school_data.gallery.date_column');
        $eventColumn = config('school_data.gallery.event_column');
        $schema = Schema::connection($connection);

        if (!$schema->hasColumn($table, $pathColumn)) {
            return $this->localGallery();
        }

        $columns = array_values(array_unique(array_filter([$titleColumn, $pathColumn, $dateColumn, $eventColumn])));

        if (empty($columns)) {
            return $this->localGallery();
        }

        $query = DB::connection($connection)->table($table);

        if ($dateColumn && $schema->hasColumn($table, $dateColumn)) {
            $query->orderByDesc($dateColumn);
        } else {
            $query->orderByDesc($pathColumn);
        }

        $rows = $query->limit(8)->get($columns);

        $gallery = [];

        foreach ($rows as $row) {
            $gallery[] = [
                'title' => $row->{$titleColumn} ?? 'Kegiatan DSCMKids',
                'path' => $row->{$pathColumn} ?? null,
                'date' => isset($row->{$dateColumn}) ? Carbon::parse($row->{$dateColumn})->format('d M Y') : null,
                'event_name' => ($eventColumn && isset($row->{$eventColumn})) ? (string) $row->{$eventColumn} : null,
                'external' => true,
            ];
        }

        return !empty($gallery) ? $gallery : $this->localGallery();
    }

    private function isPresent(mixed $value): bool
    {
        $presentValues = collect(config('school_data.attendance.present_values', []))
            ->map(fn ($item) => strtolower(trim((string) $item)))
            ->filter()
            ->values();

        return $presentValues->contains(strtolower(trim((string) $value)));
    }

    private function localGallery(): array
    {
        return Media::query()
            ->latest()
            ->take(8)
            ->get()
            ->map(fn (Media $media) => [
                'title' => $media->title,
                'path' => asset('storage/'.$media->file_path),
                'date' => optional($media->created_at)->format('d M Y'),
                'event_name' => null,
                'external' => false,
            ])
            ->toArray();
    }

    private function fallbackData(): array
    {
        $daySeed = now()->dayOfYear;
        $studentTotal = 190 + ($daySeed % 35);
        $series = $this->generateFallbackSeries();
        $class = $this->fallbackClassAttendance($studentTotal);

        $todayRate = $series[count($series) - 1]['value'];
        $todayAttendance = $class['present_total'];

        return [
            'source' => 'local-fallback',
            'metrics' => [
                'students_total' => $studentTotal,
                'attendance_today' => $todayAttendance,
                'attendance_rate' => round($todayRate, 1),
                'weekly_average' => round(collect($series)->avg('value'), 1),
                'active_classes' => count(config('school_data.class_rollup', [])),
            ],
            'attendance_series' => $series,
            'attendance_by_class' => $class['classes'],
            'attendance_totals' => [
                'present' => $todayAttendance,
                'absent' => max(0, $studentTotal - $todayAttendance),
            ],
            'gallery' => $this->localGallery(),
        ];
    }

    private function generateFallbackSeries(): array
    {
        $daySeed = now()->dayOfYear;
        $series = [];

        for ($i = 0; $i < 14; $i++) {
            $date = Carbon::today()->subDays(13 - $i);
            $value = 75 + (((($daySeed + $i) * 7) % 18) - 6);
            $value = max(62, min(96, $value));
            $series[] = [
                'label' => $date->translatedFormat('d M'),
                'value' => $value,
            ];
        }

        return $series;
    }

    private function fallbackClassAttendance(int $studentTotal): array
    {
        $classes = collect(config('school_data.class_rollup', ['PG', 'TKA', 'TKB', '1', '2', '3', '4', '5', '6']))
            ->values();

        $counts = [];
        $presentTotal = 0;
        $seed = now()->dayOfYear;

        foreach ($classes as $index => $class) {
            $value = 12 + ((($seed + $index) * 5) % 13);
            $counts[] = ['class' => (string) $class, 'present' => $value];
            $presentTotal += $value;
        }

        if ($presentTotal > $studentTotal && $studentTotal > 0) {
            $ratio = $studentTotal / $presentTotal;
            $presentTotal = 0;
            foreach ($counts as &$row) {
                $row['present'] = max(0, (int) floor($row['present'] * $ratio));
                $presentTotal += $row['present'];
            }
            unset($row);
        }

        return [
            'classes' => $counts,
            'present_total' => $presentTotal,
        ];
    }
}
