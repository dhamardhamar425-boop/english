<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\RegistrationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegistrationStatusRequest extends FormRequest
{
    /**
     * Perubahan status hanya untuk admin yang berwenang atas pendaftar ini.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('registration')) ?? false;
    }

    /**
     * Aturan validasi perubahan status pendaftar.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(RegistrationStatus::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
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
            'status.required' => 'Status baru wajib dipilih.',
            'status.enum' => 'Status yang dipilih tidak valid.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
