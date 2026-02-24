<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TestimonialSubmissionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        if (!Schema::hasTable('testimonials')) {
            return redirect(route('landing').'#testimoni')
                ->with('success', 'Fitur testimonial sedang dipersiapkan. Silakan coba lagi setelah update database.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role_label' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:700'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        Testimonial::create([
            'name' => trim($data['name']),
            'role_label' => trim((string) ($data['role_label'] ?? '')),
            'message' => trim($data['message']),
            'rating' => (int) $data['rating'],
            'sort_order' => 999,
            'is_active' => true,
        ]);

        return redirect(route('landing').'#testimoni')
            ->with('success', 'Terima kasih. Testimonial berhasil dikirim.');
    }
}
