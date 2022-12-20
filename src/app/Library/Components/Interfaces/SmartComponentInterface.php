<?php

namespace Backpack\CRUD\app\Library\Components\Interfaces;

use Backpack\CRUD\app\Library\Components\AttributeCollection;
use Backpack\CRUD\app\Library\Components\CollectionRepository;
use Illuminate\Support\Collection;

interface SmartComponentInterface
{
    public function __construct(AttributeCollection $attributes);

    public function getName(): string;

    public function isAttributeBlocked(string $attribute): bool;

    public function getAttribute(string $attribute);

    public function getAttributesArray(): array;

    public function attributes(): Collection;

    public static function make(string|array|Collection $name, CollectionRepository $collectionRepository): SmartComponentInterface;

    public static function makeOf(AttributeCollection $attributes);

    public static function getAttributes(): array;

    public static function getValidationRules(): array;

    public static function getDefaults(): array;

    public static function getBlockedAttributes(): array;
}
