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
            $todayRate = $attendanceSeries[count($attendanceSeries) - 1]['value'] ?? 0;
            $weeklyAverage = round(collect($attendanceSeries)->avg('value'), 1);
            $todayAttendance = (int) round(($todayRate / 100) * max($studentTotal, 1));
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
            return 0;
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

    private function fetchGallery(string $connection, string $table): array
    {
        if (!Schema::connection($connection)->hasTable($table)) {
            return $this->localGallery();
        }

        $titleColumn = config('school_data.gallery.title_column');
        $pathColumn = config('school_data.gallery.path_column');
        $dateColumn = config('school_data.gallery.date_column');
        $schema = Schema::connection($connection);

        if (!$schema->hasColumn($table, $pathColumn)) {
            return $this->localGallery();
        }

        $columns = array_values(array_unique(array_filter([$titleColumn, $pathColumn, $dateColumn])));

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
                'external' => false,
            ])
            ->toArray();
    }

    private function fallbackData(): array
    {
        $daySeed = now()->dayOfYear;
        $studentTotal = 190 + ($daySeed % 35);
        $series = $this->generateFallbackSeries();

        $todayRate = $series[count($series) - 1]['value'];

        return [
            'source' => 'local-fallback',
            'metrics' => [
                'students_total' => $studentTotal,
                'attendance_today' => (int) round(($todayRate / 100) * $studentTotal),
                'attendance_rate' => round($todayRate, 1),
                'weekly_average' => round(collect($series)->avg('value'), 1),
                'active_classes' => 9,
            ],
            'attendance_series' => $series,
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
}
