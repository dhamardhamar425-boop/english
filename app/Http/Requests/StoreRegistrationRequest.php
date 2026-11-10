<?php

namespace App\Http\Requests;

use App\Models\Setting;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRegistrationRequest extends FormRequest
{
    /**
     * Pendaftaran terbuka untuk seluruh siswa tanpa autentikasi.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rapikan input sebelum validasi (nomor WhatsApp tanpa spasi/dash).
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('phone'))) {
            $this->merge([
                'phone' => preg_replace('/[\s\-]/', '', $this->input('phone')) ?? '',
            ]);
        }
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request ini.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'digits_between:4,20', Rule::unique('registrations', 'nis')],
            'nik' => ['required', 'string', 'digits:16', Rule::unique('registrations', 'nik')],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'class' => ['required', 'string', 'max:30'],
            'phone' => ['required', 'string', 'regex:/^(\+?62|0)8[1-9][0-9]{6,12}$/'],
            'address' => ['required', 'string', 'max:500'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'full_name.max' => 'Nama lengkap maksimal 255 karakter.',
            'nis.required' => 'NIS wajib diisi.',
            'nis.digits_between' => 'NIS harus berupa angka sepanjang 4-20 digit.',
            'nis.unique' => 'NIS ini sudah terdaftar. Satu siswa hanya boleh mendaftar satu kali.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus terdiri dari tepat 16 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar. Satu siswa hanya boleh mendaftar satu kali.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Jenis kelamin tidak valid.',
            'class.required' => 'Kelas wajib diisi.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Format nomor WhatsApp tidak valid. Contoh: 081234567890.',
            'address.required' => 'Alamat wajib diisi.',
            'reason.required' => 'Alasan memilih organisasi wajib diisi.',
            'reason.max' => 'Alasan maksimal 1000 karakter.',
        ];
    }

    /**
     * Pastikan periode pendaftaran sedang terbuka sebelum data divalidasi.
     *
     * @return array<int, \Closure>
     */
    public function after(): array
    {
        return [
            function ($validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                if (! Setting::isRegistrationOpen()) {
                    $validator->errors()->add(
                        'reason',
                        'Pendaftaran belum dibuka atau sudah ditutup.',
                    );
                }
            },
        ];
    }
}
