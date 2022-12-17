<?php

namespace Backpack\CRUD\app\Library\Components\Interfaces;

interface HasValidationRules
{
    public static function getValidationRules(): array;
}
