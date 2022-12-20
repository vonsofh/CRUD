<?php

namespace Backpack\CRUD\app\Library\Components\Attributes;

use Backpack\CRUD\app\Library\Components\AttributeCollection;
use Backpack\CRUD\app\Library\Components\Interfaces\AttributeInterface;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Illuminate\Support\Facades\Validator;

class BackpackAttribute implements AttributeInterface
{
    public function __construct(
                    protected string $name,
                    private $value = null,
                    private $default = null,
                    private $rules = []
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

    public static function make(string $name, $value = null, $default = [], $rules = [])
    {
        return new static($name, $value, $default, $rules);
    }

    public static function getDefault(AttributeCollection $attributes)
    {
        return null;
    }

    public static function getAttributeName(): string
    {
        return static::$name;
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

    public function rules(): array
    {
        return $this->rules;
    }

    public function value()
    {
        return $this->value;
    }

    private function validate($value)
    {
        $validator = Validator::make([$this->name => $value], [$this->name => $this->rules])->stopOnFirstFailure();

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
    }
}
