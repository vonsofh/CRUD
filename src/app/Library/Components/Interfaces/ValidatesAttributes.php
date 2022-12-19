<?php

namespace Backpack\CRUD\app\Library\Components\Interfaces;

interface ValidatesAttributes
{
    public static function getAttributeValidationRules(): array;
}
