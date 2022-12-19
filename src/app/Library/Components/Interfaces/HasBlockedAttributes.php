<?php

namespace Backpack\CRUD\app\Library\Components\Interfaces;

interface HasBlockedAttributes
{
    public static function getBlockedAttributes(): array;

    public function isAttributeBlocked(string $attribute): bool;
}
