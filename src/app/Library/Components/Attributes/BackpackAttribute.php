<?php

namespace Backpack\CRUD\app\Library\Components\Attributes;

use Backpack\CRUD\app\Library\Components\AttributeCollection;
use Backpack\CRUD\app\Library\Components\Interfaces\ComponentCollection;
use Backpack\CRUD\app\Library\Components\Interfaces\HasDefault;
use Backpack\CRUD\app\Library\Components\Interfaces\HasProvider;
use Backpack\CRUD\app\Library\Components\Interfaces\HasValidationRules;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Illuminate\Support\Facades\Validator;

class BackpackAttribute implements HasValidationRules, HasDefault, HasProvider
{
    public function __construct(
                    public string $attribute,
                    public $value = null,
                    public $default = null,
                    public $rules = []
                ) {
    }

    public static function provider(): CrudPanel
    {
        return app('crud');
    }

    public static function getValidationRules(): array
    {
        return [];
    }

    public static function make(string $attribute, $value = null, $default = [], $rules = [])
    {
        return new static($attribute, $value, $default, $rules);
    }

    public static function getDefault(AttributeCollection $attributes)
    {
        return null;
    }

    public function validate($value) {
        $validator = Validator::make([$this->attribute => $value], [$this->attribute => $this->rules])->stopOnFirstFailure();

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
    }

    public static function getAttributeName(): string
    {
        return static::$attribute;
    }

    public function setValue($value)
    {
        $this->validate($value);

        $this->value = $value;

        return $this;
    }

    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }
}
