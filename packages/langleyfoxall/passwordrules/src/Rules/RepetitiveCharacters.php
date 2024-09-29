<?php

namespace langleyfoxall\passwordrules\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class RepetitiveCharacters.
 *
 * Implements the 'Repetitive characters' recommendation
 * from NIST SP 800-63B section 5.1.1.2.
 */
class RepetitiveCharacters implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !preg_match('/(.)\1{2,}/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('The :attribute can not have repetitive characters of 3 or more.');
    }
}
