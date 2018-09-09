# Validation Framework

## Feature Requirements

1. Easy to use
2. Easy to extend
3. Basic functionality

## Usage

```php
use Crtl\Validation\Custom\Rule as CustomRule;

require_once(__DIR__ . "/vendor/autoload.php");

$validator = new Crtl\Validation\Validator([
    "ruleName" => ["fieldName1", ..., "fieldNameN"],
    "ruleName" => ["fieldName" => "configValue"],
    "ruleName" => ["fieldName" => ["config1", "config"]],
    CustomRule::class => ["fieldName", ...]
]);


if (!$validator->validate($data)) {
    $errors = $validator->getErrors();
}


```

## Define Custom Validation Rules

To define custom validation rules you can either implement the 
`Crtl\Validation\RuleInterface` or the 
`Crtl\Validation\SingleInstanceRuleInterface`.<br/>
<br/>
The `Crtl\Validation\SingleInstanceRuleInterface` will be only instantiated once.

```php

class UniqueDbColumnRule implements \Crtl\Validation\RuleInterface {

    protected $connection;
    protected $table;
    protected $column;

    public function __construct(array $config) {
        $this->connection = $config[0] ?? null;
        $this->table = $config[0] ?? null;
        $this->column = $config[0] ?? null;
    }
    
    public function getName() {
        return self::class;
    }
    
    public function validate($value): bool {
        $stmt = $this->db->prepare(
            sprintf("SELECT %$2s FROM %$s WHERE %$2s=? LIMIT 1;", $this->table, $this->column)
        );
        
        $stmt->bindValue(1, $value);
        $stmt->execute();
        return $stmt->rowCount() === 0;
    }

}

$validator = new Crtl\Validation\Validator([
    UniqueDbColumnRule::class => [
        "username" => [$db, "users", "username"],
        "email" => [$db, "users", "email"]
    ]
]);

$validator->validate($data);

```

## Errors

Errors can be retrieved by calling `Crtl\Validation\Validator::getErrors`.
Errors are resetted by calling `Crtl\Validation\Validator::validate`.

 `Crtl\Validation\Validator::getErrors` returns an array with the following format:
 
 ```
 [
    "fieldName" => ["validatorName1", "validatorName2", ...],
    ...
 ]
 ```
