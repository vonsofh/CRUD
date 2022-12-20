<?php

namespace Backpack\CRUD\app\Library\Components\Interfaces;
use Backpack\CRUD\app\Library\Components\AttributeCollection;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;

interface AttributeInterface
{
    public static function getAttributeName(): string;

    public function rules(): array;

    public function value();

    public static function getDefault(AttributeCollection $attributes);

    public static function provider(): CrudPanel;

    public static function getValidationRules(): array;
}
