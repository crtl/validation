<?php
/**
 * Created by PhpStorm.
 * User: Marvin Petker
 * Date: 09.09.2018
 * Time: 13:11
 */

namespace Crtl\Validation\Custom;


class Rule implements RuleInterface
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function validate($value): bool
    {
        return true;
    }

    public function getName(): string
    {
        return "custom";
    }


}