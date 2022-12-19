<?php

namespace Backpack\CRUD\app\Library\Components\Attributes;

use Backpack\CRUD\app\Library\Components\AttributeCollection;
use Backpack\CRUD\app\Library\Components\Interfaces\ComponentAttributeInterface;

class RelationTypeAttribute extends BackpackAttribute implements ComponentAttributeInterface
{
    public static function getDefault(AttributeCollection $attributes)
    {
        return $attributes->getAttributeValue('entity') ? static::provider()->inferRelationTypeFromRelationship($attributes->toArray()) : false;
    }

    public static function getValidationRules(): array
    {
        return ['nullable|string'];
    }

    public static function getAttributeName(): string
    {
        return 'relation_type';
    }
}
