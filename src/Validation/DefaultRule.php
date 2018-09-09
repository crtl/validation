<?php
/**
 * Created by PhpStorm.
 * User: Marvin Petker
 * Date: 09.09.2018
 * Time: 12:19
 */

namespace Crtl\Validation;


use Prophecy\Exception\InvalidArgumentException;

class DefaultRule implements RuleInterface
{
    protected $name;
    protected $value;
    protected $config;
    protected $isNamedConfig;

    const REQUIRED = "required";
    const IP = "ip";
    const IPV4 = "ipv4";
    const IPV6 = "ipv6";
    const EMAIL = "email";
    const MIN_LENGTH = "minLength";
    const MAX_LENGTH = "maxLength";
    const MAC = "mac";
    const URL = "url";
    const DOMAIN = "domain";
    const BOOLEAN = "boolean";
    const ALNUM = "alnum";
    const DIGIT = "digit";
    const PATTERN = "pattern";
    const NUMBER = "number";
    const FLOAT = "float";
    const INT = "int";
    const STRING = "string";
    const ARRAY = "array";
    const MIN = "min";
    const MAX = "max";
    const COUNT = "count";
    const MIN_COUNT = "minCount";
    const MAX_COUNT = "maxCount";
    const EQUALS = "equals";

    public function __construct(array $config)
    {

        $this->name = $config[0];
        $this->config = array_slice($config, 1);
    }

    public function validate($value): bool
    {
        $this->value = $value;
        $method = "validate" . ucfirst($this->name);

        if (!method_exists($this, $method)) {
            throw new InvalidArgumentException("Unknown validator: {$this->name}");
        }

        return $this->{$method}();
    }

    public function getName(): string {
        return $this->name;
    }

    protected function validateRequired() {
        return !empty($this->value);
    }

    protected function validateBoolean() {
        return filter_var($this->value, FILTER_VALIDATE_BOOLEAN, $this->config) !== false;
    }

    protected function validateNumber() {
        return is_numeric($this->value);
    }

    protected function validateFloat() {
        return is_float($this->value);
    }

    protected function validateInt() {
        return is_int($this->value);
    }

    protected function validateMin() {
        if ($this->config[1] ?? false) {
            return $this->value > $this->config[0];
        }
        return $this->value >= $this->config[0];
    }

    protected function validateMax() {
        if ($this->config[1] ?? false) {
            return $this->value < $this->config[0];
        }
        return $this->value <= $this->config[0];
    }

    protected function validateString() {
        return is_string($this->value);
    }

    protected function validateLength() {
        return strlen($this->value) === $this->config[0];
    }

    protected function validateMinLength() {
        $length = strlen($this->value);
        if ($this->config[1] ?? false) {
            return $length > $this->value;
        }

        return $length >= $this->value;
    }

    protected function validateMaxLength() {
        $length = strlen($this->value);
        if ($this->config[1] ?? false) {
            return $length < $this->value;
        }

        return $length <= $this->value;
    }

    protected function validateArray() {
        return is_array($this->value);
    }

    protected function validateMinCount() {
        if ($this->config[1] ?? false) {
            return is_array($this->value) && count($this->value) > $this->config[0];
        }
        return is_array($this->value) && count($this->value) >= $this->config[0];
    }

    protected function validateMaxCount() {
        if ($this->config[1] ?? false) {
            return is_array($this->value) && count($this->value) < $this->config[0];
        }
        return is_array($this->value) && count($this->value) <= $this->config[0];
    }

    protected function validateCount() {
        return is_array($this->value) && count($this->value) === $this->config[0];
    }

    protected function validateEmail() {
        return filter_var($this->value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateIp() {
        return filter_var($this->value, FILTER_VALIDATE_IP) !== false;
    }

    protected function validateIpv6() {
        return filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    protected function validateIpv4() {
        return filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    protected function validatePattern() {
        return preg_match($this->config[0], $this->value) === 1;
    }

    protected function validateAlnum() {
        return ctype_alnum($this->value);
    }

    protected function validateDigit() {
        return ctype_digit($this->value);
    }

    protected function validateDomain() {
        return filter_var($this->value, FILTER_VALIDATE_DOMAIN, $this->config) !== false;
    }

    protected function validateUrl() {
        return filter_var($this->value, FILTER_VALIDATE_URL, $this->config) !== false;
    }



    protected function validateMac() {
        return filter_var($this->value, FILTER_VALIDATE_MAC, $this->config) !== false;
    }

    protected function validateEquals() {
        if ($this->config[1] ?? false) {
            return $this->value === $this->config[0];
        }

        return $this->value == $this->config[1];
    }

}