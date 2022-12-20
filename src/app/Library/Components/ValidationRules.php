<?php

namespace Backpack\CRUD\app\Library\Components;

use Backpack\CRUD\app\Library\Components\Interfaces\AttributeInterface;

class ValidationRules
{
    public function __construct(private array $rules)
    {
        $this->rules = collect($rules)->mapWithKeys(function ($rule, $attribute) {
            if (is_a($rule, AttributeInterface::class, true)) {
                return [$rule::getAttributeName() => $rule::getValidationRules()];
            }

            return [$attribute =>  $rule];
        })->toArray();
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function for($attribute)
    {
        return $this->rules[$attribute] ?? [];
    }
}
