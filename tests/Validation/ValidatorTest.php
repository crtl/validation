<?php
/**
 * Created by PhpStorm.
 * User: Marvin Petker
 * Date: 09.09.2018
 * Time: 10:57
 */

namespace Crtl\Test\Validation;


use Crtl\Validation\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{

    public function testCanInstantiateValidator() {
        $this->assertInstanceOf(Validator::class, new Validator());
    }

    public function testParseRules() {
        $validator = new Validator([
            "required" => ["username", "password", "email"],
            "email" => "email",
            "minLength" => [
                "username" => 5,
                "password" => [6],
            ],
            "maxLength" => [
                "username" => 32
            ],
            "unique" => [
                "email" => [
                    "default",
                    "table",
                    "column"
                ]
            ]
        ]);

        $rules = $validator->getRules();

        $this->assertCount(5, $rules);
        $this->assertArrayHasKey("required", $rules);
        $this->assertArrayHasKey("email", $rules);
        $this->assertArrayHasKey("minLength", $rules);
        $this->assertArrayHasKey("maxLength", $rules);
        $this->assertArrayHasKey("unique", $rules);

        $this->assertCount(3, $rules["required"]);
        $this->assertCount(1, $rules["email"]);
        $this->assertCount(1, $rules["maxLength"]);
        $this->assertCount(2, $rules["minLength"]);
        $this->assertCount(1, $rules["unique"]);
    }

    public function testCanValidate() {
        $validator = new Validator([
            "required" => ["username", "password", "email"],
            "email" => "email",
            "pattern" => [
                "username" => ["/^[a-z0-9\.\-\_]+$/i"]
            ],
            "minLength" => [
                "username" => 6,
                "password" => 8
            ]
        ]);

        $this->assertTrue(
            $validator->validate([
                "username" => "user.name",
                "password" => "asfmaslfma\"=%\")",
                "email" => "mail@mail.com"
            ])
        );

        $this->assertFalse(
            $validator->validate([
                "username" => "(/)&asfasasf)%",
                "password" => "123",
                "email" => "asasfasfasf"
            ])
        );

        $errors = $validator->getErrors();

        $this->assertArrayHasKey("username", $errors);
        $this->assertArrayHasKey("password", $errors);
        $this->assertArrayHasKey("email", $errors);
    }

}