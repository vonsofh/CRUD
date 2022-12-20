<?php

namespace Backpack\CRUD\app\Library\Components;

use Backpack\CRUD\app\Library\Components\Interfaces\SmartComponentInterface;
use Illuminate\Support\Collection;

class Component implements SmartComponentInterface
{
    public function __construct(protected AttributeCollection $attributes)
    {
        $attributes->setAttributeDefaults(static::getDefaults());
        $attributes->setValidationRules(static::getValidationRules());

        $item = $attributes->getCollectionRepository()->getItemByName($attributes->getAttributeValue('name'));

        if ($item) {
            $attributes = $item->attributes();
        } else {
            $attributes->addCollectionOfAttributes($this);
        }
    }

    public static function makeOf(AttributeCollection $attributes)
    {
        return new static($attributes);
    }

    public function attributes(): Collection
    {
        return $this->attributes->getItems();
    }

    public static function make(string|array|Collection $name, CollectionRepository $collectionRepository): SmartComponentInterface
    {
        $attributes = new AttributeCollection($name, $collectionRepository);

        return new static($attributes);
    }

    public function getAttributesArray(): array
    {
        return $this->attributes->toArray();
    }

    protected function remove()
    {
        $this->attributes->deleteItem($this->getName());
    }

    protected function setAttribute(string $attribute, $value)
    {
        $this->attributes->addAttribute($attribute, $value);
    }

    public function getName(): string
    {
        return $this->attributes->getAttributeValue('name');
    }

    public function hasAttribute(string $attribute): bool
    {
        return $this->attributes->hasAttribute($attribute);
    }

    public function getAttribute(string $attribute)
    {
        return $this->attributes->getAttributeValue($attribute);
    }

    public function __call($name, $arguments)
    {
        if (! method_exists($this, $name)) {
            if ($this->isAttributeBlocked($name)) {
                throw new \Exception("Attribute {$name} cannot be changed in this component.");
            }

            $this->setAttribute($name, $arguments[0] ?? null);

            return $this;
        }
    }

    public function isAttributeBlocked(string $attribute): bool
    {
        return in_array($attribute, $this->getBlockedAttributes());
    }

    public static function getDefaults(): array
    {
        return static::getAttributes();
    }

    public static function getValidationRules(): array
    {
        return static::getAttributes();
    }

    public static function getBlockedAttributes(): array
    {
        return static::getAttributes();
    }

    public static function getAttributes(): array
    {
        return [];
    }
}
