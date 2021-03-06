<?php
/**
 * Created by PhpStorm.
 * User: Marvin Petker
 * Date: 09.09.2018
 * Time: 12:06
 */

namespace Crtl\Validation;

/**
 * Interface RuleInterface
 * Interface to be implemented by validation rules
 * @package Crtl\Validation
 */
interface RuleInterface
{
    public function __construct(array $config);
    public function validate($value): bool;
    public function getName(): string;
}