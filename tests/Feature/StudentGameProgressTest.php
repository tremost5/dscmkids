<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StudentGameProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_quiz_points_only_increase_when_today_best_score_improves(): void
    {
        Carbon::setTestNow('2026-03-08 09:00:00');

        $student = User::factory()->create([
            'role' => 'student',
            'points' => 0,
            'streak_days' => 0,
        ]);

        $this->actingAs($student);

        $firstResponse = $this->postJson(route('student.quiz.submit'), [
            'quiz_key' => 'sunday',
            'answers' => [
                'su1' => 'rumah Tuhan',
                'su2' => 'mengenal',
                'su3' => 'fokus dan hormat',
            ],
        ]);

        $firstResponse->assertOk()
            ->assertJsonPath('best_score_today', 100)
            ->assertJsonPath('points', 100);

        $student->refresh();
        $this->assertSame(100, $student->points);
        $this->assertSame(1, $student->streak_days);

        $secondResponse = $this->postJson(route('student.quiz.submit'), [
            'quiz_key' => 'sunday',
            'answers' => [
                'su1' => 'pasar',
                'su2' => 'mengenal',
                'su3' => 'fokus dan hormat',
            ],
        ]);

        $secondResponse->assertOk()
            ->assertJsonPath('best_score_today', 100)
            ->assertJsonPath('points', 100);

        $student->refresh();
        $this->assertSame(100, $student->points);

        Carbon::setTestNow();
    }
}
