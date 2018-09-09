<?php
/**
 * Created by PhpStorm.
 * User: Marvin Petker
 * Date: 09.09.2018
 * Time: 13:12
 */

namespace Crtl\Validation\Custom;


use Crtl\Validation\SingleInstanceRuleInterface;

class SingleRule implements SingleInstanceRuleInterface
{

    public static $instanceCount = 0;

    public function __construct(array $config)
    {
        self::$instanceCount++;
    }

    public function validate($value): bool
    {
        return true;
    }

    public function getName(): string
    {
        return "custom-single";
    }


}