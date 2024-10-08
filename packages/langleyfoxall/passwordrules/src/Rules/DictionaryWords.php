<?php

namespace langleyfoxall\passwordrules\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class DictionaryWords.
 *
 * Implements the 'Dictionary words' recommendation
 * from NIST SP 800-63B section 5.1.1.2.
 */
class DictionaryWords implements Rule
{
    const DICTIONARY_FILE = __DIR__.'/../../resources/words.txt';

    private $words = [];

    /**
     * DictionaryWords constructor.
     */
    public function __construct()
    {
        $this->words = explode("\n", file_get_contents(self::DICTIONARY_FILE));
    }



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

        $value = preg_replace("/[^a-zA-Z]/", "", $value);
        return !in_array(strtolower(trim($value)), $this->words);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('The :attribute can not be a dictionary word.');
    }
}
