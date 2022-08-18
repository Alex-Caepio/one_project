<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Validates slugs for URLs.
 * Allows just numbers, letters and '-'.
 */
class Slug implements Rule
{
    public function passes($attribute, $value): bool
    {
        return preg_match("/^[a-zA-Z0-9]+([-]?[a-zA-Z0-9]+)*$/", $value);
    }

    public function message(): string
    {
        return 'Incorrect slug. You must use letters, digits and - character.';
    }
}
