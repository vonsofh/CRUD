<?php

namespace Backpack\CRUD\app\Library\Components\Attributes;

use Backpack\CRUD\app\Library\Components\AttributeCollection;
use Backpack\CRUD\app\Library\Components\Attributes\BackpackAttribute;
use Backpack\CRUD\app\Library\Components\Interfaces\ComponentAttributeInterface;

class MultipleAttribute extends BackpackAttribute implements ComponentAttributeInterface
{
    public static function getDefault(AttributeCollection $attributes)
    {
        switch ($attributes->getAttributeValue('relation_type')) {
            case 'BelongsToMany':
            case 'HasMany':
            case 'HasManyThrough':
            case 'HasOneOrMany':
            case 'MorphMany':
            case 'MorphOneOrMany':
            case 'MorphToMany':
                return true;
            default:
                return false;
        }
        return false;
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
