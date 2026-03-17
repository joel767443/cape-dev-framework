<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp\Http\Requests;

/**
 * BaseRequest provides validation capabilities for request payloads.
 */
class BaseRequest
{
    public array $errors = [];

    public const RULE_REQUIRED = 'required';
    public const IS_INT = 'is int';
    public const IS_FLOAT = 'is float';
    public const IS_STRING = 'is string';
    public const IS_BOOL = 'is bool';
    public const IS_EMAIL = 'is email';
    public const IS_URL = 'is url';
    public const IS_DATE = 'is date';
    public const DATE_FORMAT = 'date format';
    public const MIN = 'min';
    public const MAX = 'max';
    public const MIN_LENGTH = 'min length';
    public const MAX_LENGTH = 'max length';
    public const IN = 'in';
    public const REGEX = 'regex';
    public const SAME = 'same';

    /**
     * @param array $rules
     * @param array $data
     * @return bool
     */
    public function validate(array $rules, array $data): bool
    {
        $this->errors = [];

        foreach ($rules as $attribute => $attributeRules) {
            $value = $data[$attribute] ?? null;
            $isEmpty = $this->isEmptyForRequired($value);
            $hasRequiredRule = in_array(self::RULE_REQUIRED, $attributeRules, true);

            // If a field is optional and empty, skip all other validations.
            if ($isEmpty && !$hasRequiredRule) {
                continue;
            }

            foreach ($attributeRules as $rule) {
                $ruleName = $rule;
                $ruleParams = [];

                if (!is_string($ruleName)) {
                    $ruleName = $rule[0];
                    $ruleParams = array_slice($rule, 1);
                }

                if ($ruleName === self::RULE_REQUIRED && $this->isEmptyForRequired($value)) {
                    $this->addError($attribute, self::RULE_REQUIRED);
                }

                if ($ruleName === self::IS_INT && !is_int($value)) {
                    $this->addError($attribute, self::IS_INT);
                }
                if ($ruleName === self::IS_FLOAT && !is_float($value)) {
                    $this->addError($attribute, self::IS_FLOAT);
                }
                if ($ruleName === self::IS_STRING && !is_string($value)) {
                    $this->addError($attribute, self::IS_STRING);
                }
                if ($ruleName === self::IS_BOOL && !is_bool($value)) {
                    $this->addError($attribute, self::IS_BOOL);
                }
                if ($ruleName === self::IS_EMAIL && (!is_string($value) || !filter_var($value, FILTER_VALIDATE_EMAIL))) {
                    $this->addError($attribute, self::IS_EMAIL);
                }
                if ($ruleName === self::IS_URL && (!is_string($value) || !filter_var($value, FILTER_VALIDATE_URL))) {
                    $this->addError($attribute, self::IS_URL);
                }
                if ($ruleName === self::IS_DATE) {
                    if (!is_string($value) || strtotime($value) === false) {
                        $this->addError($attribute, self::IS_DATE);
                    }
                }
                if ($ruleName === self::DATE_FORMAT) {
                    $format = $ruleParams[0] ?? '';
                    if (!is_string($value) || !is_string($format) || $format === '') {
                        $this->addError($attribute, self::DATE_FORMAT, ['format' => $format]);
                    } else {
                        $dt = \DateTime::createFromFormat($format, $value);
                        $ok = $dt && $dt->format($format) === $value;
                        if (!$ok) {
                            $this->addError($attribute, self::DATE_FORMAT, ['format' => $format]);
                        }
                    }
                }
                if ($ruleName === self::MIN) {
                    $min = $ruleParams[0] ?? null;
                    if (!is_numeric($min) || !is_numeric($value) || $value < $min) {
                        $this->addError($attribute, self::MIN, ['min' => $min]);
                    }
                }
                if ($ruleName === self::MAX) {
                    $max = $ruleParams[0] ?? null;
                    if (!is_numeric($max) || !is_numeric($value) || $value > $max) {
                        $this->addError($attribute, self::MAX, ['max' => $max]);
                    }
                }
                if ($ruleName === self::MIN_LENGTH) {
                    $minLen = $ruleParams[0] ?? null;
                    if (!is_numeric($minLen) || !is_string($value) || mb_strlen($value) < (int)$minLen) {
                        $this->addError($attribute, self::MIN_LENGTH, ['min' => $minLen]);
                    }
                }
                if ($ruleName === self::MAX_LENGTH) {
                    $maxLen = $ruleParams[0] ?? null;
                    if (!is_numeric($maxLen) || !is_string($value) || mb_strlen($value) > (int)$maxLen) {
                        $this->addError($attribute, self::MAX_LENGTH, ['max' => $maxLen]);
                    }
                }
                if ($ruleName === self::IN) {
                    $set = $ruleParams[0] ?? null;
                    if (!is_array($set) || !in_array($value, $set, true)) {
                        $this->addError($attribute, self::IN);
                    }
                }
                if ($ruleName === self::REGEX) {
                    $pattern = $ruleParams[0] ?? null;
                    if (!is_string($pattern) || $pattern === '' || !is_string($value) || @preg_match($pattern, $value) !== 1) {
                        $this->addError($attribute, self::REGEX);
                    }
                }
                if ($ruleName === self::SAME) {
                    $otherAttribute = $ruleParams[0] ?? null;
                    if (!is_string($otherAttribute) || ($data[$otherAttribute] ?? null) !== $value) {
                        $this->addError($attribute, self::SAME, ['other' => (string)$otherAttribute]);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function isEmptyForRequired($value): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_string($value) && trim($value) === '') {
            return true;
        }

        if (is_array($value) && empty($value)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $attribute
     * @param string $rule
     * @param array $params
     * @return void
     */
    protected function addError(string $attribute, string $rule, array $params = []): void
    {
        $message = $this->formatErrorMessage($rule, $params);
        $this->errors[$attribute][] = $message;
    }

    /**
     * @param string $rule
     * @param array $params
     * @return string
     */
    protected function formatErrorMessage(string $rule, array $params = []): string
    {
        $template = $this->errorMessages()[$rule] ?? '';
        if ($template === '') {
            return '';
        }

        foreach ($params as $key => $value) {
            $template = str_replace('{' . $key . '}', (string)$value, $template);
        }

        return $template;
    }

    /**
     * @return string[]
     */
    public function errorMessages(): array
    {
        return [
            self::RULE_REQUIRED => 'This field is required.',
            self::IS_INT => 'This field must be an integer.',
            self::IS_FLOAT => 'This field must be a float.',
            self::IS_STRING => 'This field must be a string.',
            self::IS_BOOL => 'This field must be a boolean.',
            self::IS_EMAIL => 'This field must be a valid email address.',
            self::IS_URL => 'This field must be a valid URL.',
            self::IS_DATE => 'This field must be a valid date.',
            self::DATE_FORMAT => 'This field must match the date format {format}.',
            self::MIN => 'This field must be at least {min}.',
            self::MAX => 'This field must be at most {max}.',
            self::MIN_LENGTH => 'This field must be at least {min} characters.',
            self::MAX_LENGTH => 'This field must be at most {max} characters.',
            self::IN => 'This field contains an invalid value.',
            self::REGEX => 'This field format is invalid.',
            self::SAME => 'This field must match {other}.',
        ];
    }
}

