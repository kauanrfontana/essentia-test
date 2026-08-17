<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneDigitCount implements ValidationRule
{
    /**
     * Accepts the value with or without mask characters, but the digits
     * must amount to a valid DDD + phone number (10 or 11 digits).
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        if (! in_array(strlen($digits), [10, 11], true)) {
            $fail('O campo :attribute deve conter o DDD e o número (10 ou 11 dígitos).');
        }
    }
}
