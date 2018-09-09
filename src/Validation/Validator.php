<?php
/**
 * Created by PhpStorm.
 * User: Marvin Petker
 * Date: 09.09.2018
 * Time: 10:52
 */

namespace Crtl\Validation;


class Validator
{

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var array
     */
    protected $errors;

    /**
     * Validator constructor.
     * @param array $rules
     */
    public function __construct(array $rules = []) {
        $this->setRules($rules);
    }

    /**
     * @param array $rules
     */
    public function parseRules(array $rules) {
        foreach ($rules as $ruleName => $fieldsConfig) {
            $this->parseRule($ruleName, $fieldsConfig);
        }
    }

    public function setRules(array $rules) {
        $this->parseRules($rules);
    }

    public function addRule($name, $fieldConfig) {
        $this->parseRule($name, $fieldConfig);
    }

    /**
     * @param array $data
     * @param bool $sanitize True to strip fields from data which arent specidifed in rules
     */
    public function validate(array $data, $sanitize = false) {
        $this->reset();
        $valid = true;

        foreach ($this->rules as $rule => $fieldsConfig) {
            $implements = [];

            if (class_exists($rule, true)) {
                $implements = class_implements($rule);
                if (!array_key_exists(RuleInterface::class, $implements)) {
                    throw new \InvalidArgumentException("Rule '{$rule}' must implement " . RuleInterface::class);
                }
            } else {
                $ruleName = $rule;
                $rule = DefaultRule::class;
            }

            $instantiateOnce = array_key_exists(SingleInstanceRuleInterface::class, $implements);
            $instantiated  = false;


            foreach ($fieldsConfig as $field => $config) {
                if ($rule === DefaultRule::class) {
                    array_unshift($config, $ruleName);
                }

                if (!$instantiateOnce || !$instantiated) {
                    $validator = new $rule($config);
                    $instantiated = true;
                }

                if (!$validator->validate($data[$field] ?? null)) {
                    $this->addError($field, $validator->getName());
                    $valid = false;
                }
            }

        }

        return $valid;

    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getRules() {
        return $this->rules;
    }

    /**
     * Reset current state
     */
    public function reset() {
        $this->errors = [];
    }

    /**
     * @param string $rule
     * @param $fieldsConfig
     * @return array
     */
    protected function parseRule(string $rule, $fieldsConfig) {
        $parsedConfig = [];

        if (is_string($fieldsConfig)) {
            return $this->rules[$rule][$fieldsConfig] = [];
        }

        if (!is_array($fieldsConfig)) {
            throw new \InvalidArgumentException("Invalid configuration for rule '{$rule}': " . print_r($fieldsConfig, true));
        }

        //Transform [field1, field2, ...] to [field1 => [], field2 => [], ...]
        array_walk($fieldsConfig, function($field, $key) use (&$fieldsConfig) {
            if (is_numeric($key)) {
                $fieldsConfig[$field] = [];
                unset($fieldsConfig[$key]);
            }
        });


        foreach ($fieldsConfig as $field => $fieldConfig) {
            $this->rules[$rule][$field] = is_array($fieldConfig) ? $fieldConfig : [$fieldConfig];
        }
    }

    /**
     * @param $field
     * @param $rule
     */
    protected function addError(string $field, string $rule) {
        $this->errors[$field][] = $rule;
    }

}