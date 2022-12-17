<?php

namespace Backpack\CRUD\app\Library\Components\Interfaces;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;

interface HasProvider
{
    public static function provider(): CrudPanel;
}
