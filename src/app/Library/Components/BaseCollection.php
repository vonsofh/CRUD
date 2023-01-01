<?php

namespace Backpack\CRUD\app\Library\Components;

use Backpack\CRUD\app\Library\Components\Attributes\BaseAttribute;
use Backpack\CRUD\app\Library\Components\Interfaces\SmartAttributeInterface;
use Backpack\CRUD\app\Library\Components\Interfaces\SmartCollectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class BaseCollection implements SmartCollectionInterface
{
    private Collection $attributes;

    public function __construct(array|string $initAttributes,
                                array $componentAttributes = [],
                                array $rules = [],
                                array $defaults = [],
                                private array $blockedAttributes = []
                            ) {
        $this->attributes = $this->buildAttributes($initAttributes, $componentAttributes);

        $this->setAttributeDefaults($componentAttributes);
        
        $this->validate();

        $item = $this->getItemByName((string) $this->getAttributeValue('name'));
        if ($item) {
            $this->attributes = $this->buildAttributes((array)$item, $componentAttributes);
        } else {
            $this->addCollectionItem($this);
        }
    }

    private function buildAttributes(array|string $attributes, $componentAttributes)
    {
        if (is_string($attributes)) {
            $attributes = ['name' => $attributes];
        }

        $attributeNames = collect($componentAttributes)->mapWithKeys(function ($attribute) {
            return [$attribute::getAttributeName() => $attribute];
        })->toArray();

        return collect($attributes)->mapWithKeys(function ($value, $key) use ($attributeNames) {
            if (isset($attributeNames[$key])) {
                return [$key => $attributeNames[$key]::make($key, $value, $attributeNames[$key]::getValidationRules($this))];
            }

            return [$key => new BaseAttribute($key, $value)];
        });
    }

    public function addCollectionItem($attributes)
    {
        $collection = $this->getCollection();
        $collection[$attributes->getAttributeValue('name')] = $attributes->toArray();
        $this->saveCollection($collection);
    }

    public function getItems(): Collection
    {
        return collect($this->getCollection());
    }

    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function getItemByName(string $name)
    {
        return $this->getItems()->first(function ($item, $key) use ($name) {
            return $key === $name;
        });
    }

    public function hasAttribute(string $attribute): bool
    {
        return $this->attributes->has($attribute);
    }

    public function getAttributeValue($attribute, $default = null)
    {
        return $this->attributes->has($attribute) ? $this->attributes->get($attribute, $default)->value() : $default;
    }

    public function validate()
    {
        $attributes = $this->toArray();

        $rules = $this->getValidationRules();

        $validator = Validator::make($attributes, $rules)->stopOnFirstFailure();

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
    }

    private function getValidationRules()
    {
        $rules = [];
        foreach($this->attributes as $attribute) {
            $rules[$attribute->name()] = $attribute->rules(); 
        }
        return $rules;
    }

    public function setAttribute($attribute, $value)
    {
        $item = $this->hasAttribute($attribute) ? $this->attributes->get($attribute)->setValue($value) : new BaseAttribute($attribute, $value);
        $this->updateItem($item);
    }

    private function updateItem(SmartAttributeInterface $item)
    {
        $this->attributes[$item->name()] = $item;
        $collection = $this->getCollection();
        $collection[$this->getAttributeValue('name')] = $this->toArray();
        $this->saveCollection($collection);
    }

    public function deleteItem(string $name)
    {
        $this->attributes->forget($name);
        $collection = $this->getCollection();
        $collection->forget($name);
        $this->saveCollection($collection);
    }

    public function toCollection(): Collection
    {
        return $this->attributes->mapWithKeys(function ($item, $key) {
            return [$key => $item->value()];
        });
    }

    public function toArray(): array
    {
        return $this->toCollection()->toArray();
    }

    private function setAttributeDefaults($componentAttributes)
    {
        foreach ($componentAttributes as $attribute) {
            if (! $this->hasAttribute($attribute::getAttributeName())) {
                $this->attributes[$attribute::getAttributeName()] = $attribute::make($attribute::getAttributeName(), $attribute::getDefault($this), $attribute::getValidationRules($this));
            }
        }
    }

    public static function attributes(): array
    {
        return [];
    }

    public function getCollection()
    {
        return collect();
    }

    public function saveCollection($collection)
    {
        return [];
    }
}
