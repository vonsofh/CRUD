<?php

namespace Backpack\CRUD\app\Library\Components\Attributes\Fields;

use Backpack\CRUD\app\Library\Components\AttributeCollection;
use Backpack\CRUD\app\Library\Components\Attributes\BackpackAttribute;
use Backpack\CRUD\app\Library\Components\Interfaces\ComponentAttributeInterface;

class FieldType extends BackpackAttribute implements ComponentAttributeInterface
{
    public static function getDefault(AttributeCollection $attributes)
    {
        if (backpack_pro() && $attributes->getAttributeValue('relation_type')) {
            return 'relationship';
        }

        switch ($attributes->getAttributeValue('relation_type')) {
            case 'BelongsTo':
                return 'select';
            case 'BelongsToMany':
            case 'MorphToMany':
                return 'select_multiple';
            default:
                return 'text';
        }
    }

    public static function getValidationRules(): array
    {
        return ['required'];
    }

    public static function getAttributeName(): string
    {
        return 'type';
    }
}
