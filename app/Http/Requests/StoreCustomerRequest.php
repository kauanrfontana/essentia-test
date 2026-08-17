<?php

namespace App\Http\Requests;

use App\Rules\PhoneDigitCount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'str_name' => ['required', 'string', 'max:255'],
            'str_email' => ['required', 'email', 'max:255', Rule::unique('customers', 'str_email')],
            'str_phone' => ['required', 'string', 'max:20', new PhoneDigitCount],
            'profile_picture' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'str_name' => 'nome',
            'str_email' => 'e-mail',
            'str_phone' => 'telefone',
            'profile_picture' => 'foto',
        ];
    }
}
