<?php

namespace langleyfoxall\passwordrules\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class DerivativesOfContextSpecificWords.
 *
 * Implements the 'Context-specific words' recommendation with 'derivatives thereof'.
 * from NIST SP 800-63B section 5.1.1.2.
 */
class DerivativesOfContextSpecificWords extends ContextSpecificWords implements Rule
{
    private $detectedWord = null;

    /**
     * DerivativesOfContextSpecificWords constructor.
     *
     * @param $username
     */
    public function __construct($username)
    {
        parent::__construct($username);
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
        $value = strtolower($value);

        foreach ($this->words as $word) {
            similar_text(preg_replace("/[^a-zA-Z]/", "", $value), $word, $percentage);

            if ($percentage >= 60) {
                $this->detectedWord = $word;

                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('The :attribute can not be similar to your name.', ['word' => $this->detectedWord]);
    }
}
