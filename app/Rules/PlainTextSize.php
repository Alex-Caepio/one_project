<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PlainTextSize implements Rule
{

    private int $stringSize;
    private int $stringSizeCalculated;

    /**
     * Create a new rule instance.
     *
     * @param int $stringSize
     */
    public function __construct(int $stringSize)
    {
        $this->stringSize = $stringSize;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->stringSizeCalculated = strlen(strip_tags($value));
        return is_string($value) && $this->stringSizeCalculated <= $this->stringSize;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute may not be greater than '.$this->stringSize.' characters. You provided: '.$this->stringSizeCalculated;
    }
}
