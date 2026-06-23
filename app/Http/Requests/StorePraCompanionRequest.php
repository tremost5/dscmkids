<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePraCompanionRequest extends FormRequest
{
    private const CLASS_OPTIONS = ['PG', 'TKA', 'TKB', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'companion_name' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\s-]+$/'],
            'children' => ['required', 'array', 'min:1'],
            'children.*.class_before' => ['required', 'string', Rule::in(self::CLASS_OPTIONS)],
            'children.*.event_registration_id' => ['required', 'integer', 'distinct'],
            'attend_26_june' => ['nullable', 'boolean'],
            'attend_27_june' => ['nullable', 'boolean'],
            'payment_method' => ['required', Rule::in(['cash', 'transfer'])],
            'payment_proof' => ['nullable', 'required_if:payment_method,transfer', 'file', 'max:4096', 'mimes:jpg,jpeg,png,webp,pdf'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (!$this->boolean('attend_26_june') && !$this->boolean('attend_27_june')) {
                $validator->errors()->add('attendance', 'Minimal satu tanggal kehadiran wajib dipilih.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'companion_name' => 'nama pendamping',
            'whatsapp_number' => 'nomor WhatsApp',
            'children' => 'daftar anak',
            'children.*.class_before' => 'kelas anak',
            'children.*.event_registration_id' => 'nama murid',
            'attend_26_june' => 'kehadiran 26 Juni 2026',
            'attend_27_june' => 'kehadiran 27 Juni 2026',
            'payment_method' => 'metode pembayaran',
            'payment_proof' => 'bukti pembayaran',
        ];
    }
}
