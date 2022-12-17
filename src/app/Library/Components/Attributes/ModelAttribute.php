<?php

namespace Backpack\CRUD\app\Library\Components\Attributes;

use Backpack\CRUD\app\Library\Components\AttributeCollection;
use Backpack\CRUD\app\Library\Components\Attributes\BackpackAttribute;
use Backpack\CRUD\app\Library\Components\Interfaces\ComponentAttributeInterface;

class ModelAttribute extends BackpackAttribute implements ComponentAttributeInterface
{
    public static function getDefault(AttributeCollection $attributes)
    {
        return $attributes->getAttributeValue('relation_type') ? static::provider()->inferFieldModelFromRelationship($attributes->toArray()) : false;
    }

    public static function getValidationRules(): array
    {
        return [
            'required',
            'string',
            function ($attribute, $value, $fail) {
                if (! is_a($value, 'Illuminate\Database\Eloquent\Model', true)) {
                    $fail('The '.$attribute.' should be a valid class that extends Illuminate\Database\Eloquent\Model .');
                }
            },
        ];
    }

    public static function getAttributeName(): string
    {
        return 'model';
    }
}
