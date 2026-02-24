<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyQuizBank;
use App\Models\DailyQuizOption;
use App\Models\DailyQuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DailyQuizBankController extends Controller
{
    public function index()
    {
        $banks = DailyQuizBank::query()
            ->withCount('questions')
            ->orderBy('day_key')
            ->paginate(20);

        return view('admin.quiz-banks.index', compact('banks'));
    }

    public function create()
    {
        return view('admin.quiz-banks.create', [
            'dayKeys' => $this->dayKeys(),
            'sampleJson' => $this->sampleJson(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $questionPayload = $this->decodeAndValidateQuestionJson($validated['questions_json']);

        DB::transaction(function () use ($validated, $questionPayload) {
            $bank = DailyQuizBank::create([
                'day_key' => $validated['day_key'],
                'title' => $validated['title'],
                'memory_verse' => $validated['memory_verse'] ?: null,
                'is_active' => (bool) ($validated['is_active'] ?? false),
            ]);

            $this->syncQuestions($bank->id, $questionPayload);
        });

        return redirect()->route('admin.quiz-banks.index')->with('success', 'Bank soal harian berhasil dibuat.');
    }

    public function edit(DailyQuizBank $quiz_bank)
    {
        $quiz_bank->load('questions.options');

        return view('admin.quiz-banks.edit', [
            'quizBank' => $quiz_bank,
            'dayKeys' => $this->dayKeys(),
            'sampleJson' => $this->sampleJson(),
            'existingJson' => json_encode(
                $quiz_bank->questions->map(function (DailyQuizQuestion $question) {
                    return [
                        'question' => $question->question_text,
                        'options' => $question->options->map(function (DailyQuizOption $option) {
                            return [
                                'text' => $option->option_text,
                                'is_correct' => $option->is_correct,
                            ];
                        })->values()->all(),
                    ];
                })->values()->all(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ),
        ]);
    }

    public function update(Request $request, DailyQuizBank $quiz_bank)
    {
        $validated = $this->validateRequest($request, $quiz_bank->id);
        $questionPayload = $this->decodeAndValidateQuestionJson($validated['questions_json']);

        DB::transaction(function () use ($validated, $quiz_bank, $questionPayload) {
            $quiz_bank->update([
                'day_key' => $validated['day_key'],
                'title' => $validated['title'],
                'memory_verse' => $validated['memory_verse'] ?: null,
                'is_active' => (bool) ($validated['is_active'] ?? false),
            ]);

            $quiz_bank->questions()->delete();
            $this->syncQuestions($quiz_bank->id, $questionPayload);
        });

        return redirect()->route('admin.quiz-banks.index')->with('success', 'Bank soal harian berhasil diperbarui.');
    }

    public function destroy(DailyQuizBank $quiz_bank)
    {
        $quiz_bank->delete();

        return redirect()->route('admin.quiz-banks.index')->with('success', 'Bank soal harian berhasil dihapus.');
    }

    private function validateRequest(Request $request, ?int $bankId = null): array
    {
        return $request->validate([
            'day_key' => [
                'required',
                Rule::in(array_keys($this->dayKeys())),
                Rule::unique('daily_quiz_banks', 'day_key')->ignore($bankId),
            ],
            'title' => ['required', 'string', 'max:180'],
            'memory_verse' => ['nullable', 'string', 'max:120'],
            'questions_json' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function decodeAndValidateQuestionJson(string $rawJson): array
    {
        $decoded = json_decode($rawJson, true);
        if (!is_array($decoded) || empty($decoded)) {
            throw ValidationException::withMessages([
                'questions_json' => 'Format JSON soal tidak valid atau kosong.',
            ]);
        }

        foreach ($decoded as $qIndex => $questionRow) {
            if (!is_array($questionRow)) {
                throw ValidationException::withMessages([
                    'questions_json' => 'Setiap item soal harus berupa object.',
                ]);
            }

            $questionText = trim((string) ($questionRow['question'] ?? ''));
            $options = $questionRow['options'] ?? null;
            if ($questionText === '' || !is_array($options) || count($options) < 2) {
                throw ValidationException::withMessages([
                    'questions_json' => 'Setiap soal wajib punya teks dan minimal 2 opsi jawaban.',
                ]);
            }

            $hasCorrect = false;
            foreach ($options as $oIndex => $optionRow) {
                $optionText = trim((string) ($optionRow['text'] ?? ''));
                $isCorrect = (bool) ($optionRow['is_correct'] ?? false);

                if ($optionText === '') {
                    throw ValidationException::withMessages([
                        'questions_json' => "Opsi jawaban kosong pada soal #".($qIndex + 1).'.',
                    ]);
                }

                if ($isCorrect) {
                    $hasCorrect = true;
                }
            }

            if (!$hasCorrect) {
                throw ValidationException::withMessages([
                    'questions_json' => "Soal #".($qIndex + 1).' harus memiliki minimal 1 jawaban benar.',
                ]);
            }
        }

        return $decoded;
    }

    private function syncQuestions(int $bankId, array $questionPayload): void
    {
        foreach ($questionPayload as $questionIndex => $questionRow) {
            $question = DailyQuizQuestion::create([
                'daily_quiz_bank_id' => $bankId,
                'question_text' => trim((string) $questionRow['question']),
                'sort_order' => $questionIndex + 1,
                'is_active' => true,
            ]);

            foreach ($questionRow['options'] as $optionIndex => $optionRow) {
                DailyQuizOption::create([
                    'daily_quiz_question_id' => $question->id,
                    'option_text' => trim((string) $optionRow['text']),
                    'is_correct' => (bool) ($optionRow['is_correct'] ?? false),
                    'sort_order' => $optionIndex + 1,
                ]);
            }
        }
    }

    private function dayKeys(): array
    {
        return [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ];
    }

    private function sampleJson(): string
    {
        $sample = [
            [
                'question' => 'Yesus berkata kita harus saling ...',
                'options' => [
                    ['text' => 'mengasihi', 'is_correct' => true],
                    ['text' => 'membandingkan', 'is_correct' => false],
                    ['text' => 'menghindari', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Saat takut, kita perlu ...',
                'options' => [
                    ['text' => 'berdoa', 'is_correct' => true],
                    ['text' => 'menyerah', 'is_correct' => false],
                ],
            ],
        ];

        return (string) json_encode($sample, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
