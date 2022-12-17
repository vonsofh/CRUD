<?php

namespace  Backpack\CRUD\app\Library\Components\Fields;
use Backpack\CRUD\app\Library\Components\ComponentCollection;
use Backpack\CRUD\app\Library\Components\Field;
use Backpack\CRUD\app\Library\Components\AttributeCollection;

class TextField extends Field 
{
    public function __construct(AttributeCollection $attributes)
    {
         parent::__construct($attributes);
         $this->setAttribute('type', 'text');
    }

    public static function getBlockedAttributes(): array
    {
        return ['type'];
    }
}