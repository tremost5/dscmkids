<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', 'max:120'],
            'has_allergy' => ['required', Rule::in(['yes', 'no'])],
            'allergy_notes' => ['nullable', 'required_if:has_allergy,yes', 'string', 'max:1000'],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'class_before' => ['required', Rule::in(['PG', 'TKA', 'TKB', '1', '2', '3', '4', '5', '6', '7', '8', '9'])],
            'church_branch' => ['required', Rule::in(['NICC', 'GRASA'])],
            'parent_name' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\s-]+$/'],
            'address' => ['required', 'string', 'max:1500'],
            'payment_method' => ['required', Rule::in(['cash', 'transfer'])],
            'payment_proof' => ['nullable', 'required_if:payment_method,transfer', 'file', 'max:4096', 'mimes:jpg,jpeg,png,webp,pdf'],
        ];
    }

    public function attributes(): array
    {
        return [
            'full_name' => 'nama lengkap',
            'nickname' => 'nama panggilan',
            'has_allergy' => 'alergi makanan / obat',
            'allergy_notes' => 'keterangan alergi',
            'birth_date' => 'tanggal lahir',
            'gender' => 'jenis kelamin',
            'class_before' => 'kelas sebelum kenaikan',
            'church_branch' => 'beribadah Sekolah Minggu di',
            'parent_name' => 'nama orang tua / wali',
            'whatsapp_number' => 'nomor WhatsApp',
            'address' => 'alamat lengkap',
            'payment_method' => 'metode pembayaran',
            'payment_proof' => 'bukti pembayaran',
        ];
    }
}
