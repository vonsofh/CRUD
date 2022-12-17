<?php

namespace Backpack\CRUD\app\Library\Components\Interfaces;

use Backpack\CRUD\app\Library\Components\AttributeCollection;

interface HasDefault
{
    public static function getDefault(AttributeCollection $attributes);
}