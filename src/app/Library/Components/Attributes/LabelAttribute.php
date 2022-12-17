<?php

namespace Backpack\CRUD\app\Library\Components\Attributes;

use Backpack\CRUD\app\Library\Components\AttributeCollection;
use Backpack\CRUD\app\Library\Components\Attributes\BackpackAttribute;
use Backpack\CRUD\app\Library\Components\Interfaces\ComponentAttributeInterface;

class LabelAttribute extends BackpackAttribute implements ComponentAttributeInterface
{
    public static function getDefault(AttributeCollection $attributes)
    {
        return ucfirst($attributes->getAttributeValue('name'));
    }

    public static function getValidationRules(): array
    {
        return ['required', 'string'];
    }

    public static function getAttributeName(): string
    {
        return 'label';
    }
}
