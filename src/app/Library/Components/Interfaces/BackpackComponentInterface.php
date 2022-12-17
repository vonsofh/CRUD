<?php

namespace Backpack\CRUD\app\Library\Components\Interfaces;

use Backpack\CRUD\app\Library\Components\AttributeCollection;
use Backpack\CRUD\app\Library\Components\CollectionRepository;
use Illuminate\Support\Collection;

interface BackpackComponentInterface
{
    public function __construct(AttributeCollection $attributes);

    public function getName(): string;

    public static function make(string|array|Collection $name, CollectionRepository $collectionRepository): BackpackComponentInterface;

    public function attributes(): Collection;

    public static function makeOf(AttributeCollection $attributes);

    public static function getAttributes(): array;

    public function getAttribute(string $attribute);

    public function getAttributesArray(): array;
}
