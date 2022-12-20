<?php

namespace Backpack\CRUD\app\Library\Components;

use Backpack\CRUD\app\Library\Components\Interfaces\AttributeInterface;

class AttributeDefaults
{
    public function __construct(private array $defaults)
    {
        dump($defaults);
        $this->defaults = collect($defaults)->mapWithKeys(function ($default, $attribute) {
            if (is_a($default, AttributeInterface::class, true)) {
                return [$default::getAttributeName() => $default];
            }

            return [$attribute =>  $default];
        })->toArray();
    }

    public function getDefaults(): array
    {
        return $this->defaults;
    }

    public function for($attribute)
    {
        return $this->defaults[$attribute] ?? [];
    }
}
