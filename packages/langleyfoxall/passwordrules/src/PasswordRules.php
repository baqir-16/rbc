<?php

namespace langleyfoxall\passwordrules;

// use LangleyFoxall\LaravelNISTPasswordRules\Rules\BreachedPasswords;
use langleyfoxall\passwordrules\Rules\ContextSpecificWords;
use langleyfoxall\passwordrules\Rules\DerivativesOfContextSpecificWords;
use langleyfoxall\passwordrules\Rules\DictionaryWords;
use langleyfoxall\passwordrules\Rules\RepetitiveCharacters;
use langleyfoxall\passwordrules\Rules\SequentialCharacters;

abstract class PasswordRules
{
    public static function register($username)
    {
        return [
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/',
            new SequentialCharacters(),
            new RepetitiveCharacters(),
            new DictionaryWords(),
            new ContextSpecificWords($username),
            new DerivativesOfContextSpecificWords($username),
        ];
    }
      public static function changePassword($username, $oldPassword = null)
    {
        $rules = self::register($username);
        if ($oldPassword) {
            $rules = array_merge($rules, [
                'different:'.$oldPassword,
            ]);
        }
        return $rules;
    }
    public static function optionallyChangePassword($username, $oldPassword = null)
    {
        $rules = self::changePassword($username, $oldPassword);
        $rules = array_merge($rules, [
            'nullable',
        ]);
        foreach ($rules as $key => $rule) {
            if (is_string($rule) && $rule === 'required') {
                unset($rules[$key]);
            }
        }
        return $rules;
    }
    public static function login()
    {
        return [
            'required',
            'string',
        ];
    }
}
