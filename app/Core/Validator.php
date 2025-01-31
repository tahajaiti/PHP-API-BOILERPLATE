<?php

namespace app\Core;

class Validator
{
    private static array $data = [];
    private static array $rules = [];
    private static array $errors = [];
    private static array $messages = [
        'required' => 'The :field field is required.',
        'email' => 'The :field must be a valid email address.',
        'min' => 'The :field must be at least :param characters.',
        'max' => 'The :field must not exceed :param characters.',
        'numeric' => 'The :field must be a number.',
        'string' => 'The :field must contain only letters.',
        'alphanumeric' => 'The :field must contain only letters and numbers.',
        'url' => 'The :field must be a valid URL.',
        'date' => 'The :field must be a valid date.',
        'in' => 'The selected :field is invalid.',
    ];

    public static function make(array $data, array $rules): bool
    {
        self::$data = $data;
        self::$rules = $rules;
        self::$errors = [];

        foreach ($rules as $field => $fieldRules) {
            $fieldRules = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;

            foreach ($fieldRules as $rule) {
                $parameters = [];

                if (str_contains($rule, ':')) {
                    [$rule, $parameter] = explode(':', $rule, 2);
                    $parameters = explode(',', $parameter);
                }

                $method = 'validate' . ucfirst($rule);

                if (method_exists(self::class, $method)) {
                    $value = $data[$field] ?? null;
                    if (!self::$method($field, $value, $parameters)) {
                        self::addError($field, $rule, $parameters);
                    }
                }
            }
        }

        return empty(self::$errors);
    }

    public static function errors(): array
    {
        return self::$errors;
    }

    private static function addError(string $field, string $rule, array $parameters = []): void
    {
        $message = self::$messages[$rule] ?? "The :field field is invalid.";
        $message = str_replace(':field', $field, $message);

        if (!empty($parameters)) {
            $message = str_replace(':param', $parameters[0], $message);
        }

        self::$errors[] = $message;
    }

    private static function validateRequired(string $field, $value, array $parameters): bool
    {
        return !empty($value) || $value === '0' || $value === 0;
    }

    private static function validateEmail(string $field, $value, array $parameters): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private static function validateMin(string $field, $value, array $parameters): bool
    {
        return strlen($value) >= (int)$parameters[0];
    }

    private static function validateMax(string $field, $value, array $parameters): bool
    {
        return strlen($value) <= (int)$parameters[0];
    }

    private static function validateNumeric(string $field, $value, array $parameters): bool
    {
        return is_numeric($value);
    }

    private static function validateString(string $field, $value, array $parameters): bool
    {
        return ctype_alpha($value);
    }

    private static function validateAlphanumeric(string $field, $value, array $parameters): bool
    {
        return ctype_alnum($value);
    }
    private static function validateUrl(string $field, $value, array $parameters): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    private static function validateDate(string $field, $value, array $parameters): bool
    {
        return strtotime($value) !== false;
    }

    private static function validateIn(string $field, $value, array $parameters): bool
    {
        return in_array($value, $parameters, strict: true);
    }
}
