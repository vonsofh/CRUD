<?php

namespace Backpack\CRUD\app\Library\Components;

use Backpack\CRUD\app\Library\Components\Attributes\BackpackAttribute;
use Backpack\CRUD\app\Library\Components\Interfaces\BackpackComponentInterface;
use Backpack\CRUD\app\Library\Components\Interfaces\ComponentAttributeInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class AttributeCollection
{
    protected Collection $items;

    public function __construct(array|string|Collection $attributes,
                                private CollectionRepository $collectionRepository,
                                ValidationRules|array $rules = [],
                                array $defaults = []
                            ) {
        $attributes = is_string($attributes) ? collect(['name' => $attributes]) : collect($attributes);
        $rules = is_array($rules) ? new ValidationRules($rules) : $rules;
        //$defaults = is_array($defaults) ? new AttributeDefaults($defaults) : $defaults;
        if (! empty($defaults)) {
            dump($defaults);
        }
        $defaults = collect($defaults)->mapWithKeys(function ($default, $attribute) {
            dd($default);
            if (is_a($default, ComponentAttributeInterface::class, true)) {
                return [$default::getAttributeName() => $default];
            }

            return [$attribute => $default];
        })->toArray();
        if (! empty($defaults)) {
            dd($defaults);
        }
        $attributes = $attributes->mapWithKeys(function ($attribute, $key) use ($rules, $defaults) {
            if (! is_a($attribute, ComponentAttributeInterface::class, true)) {
                return [$key => new BackpackAttribute($key, $attribute, $defaults[$key] ?? null, $rules->for($key))];
            }

            return [$attribute::getAttributeName() => $attribute];
        });

        $this->items = $attributes;

        assert($this->hasAttribute('name'), 'Component name cant be empty.');
        assert(is_string($this->getAttributeValue('name')), 'Component name must be a valid string.');

        //$this->setAttributeDefaults();

        //$this->validate($this->attributes, $this->getValidationForAttributes(array_keys($this->attributes)));
    }

    public function setCollectionManager(CollectionRepository $collection)
    {
        $this->collectionRepository = $collection;

        return $collection;
    }

    public function hasAttribute($attribute)
    {
        return $this->items->has($attribute);
    }

    public function getAttributeValue($attribute, $default = null)
    {
        return $this->items->has($attribute) ? $this->items->get($attribute, $default)->value : $default;
    }

    public function getCollectionRepository()
    {
        return $this->collectionRepository;
    }

    public function validateAttribute(ComponentAttributeInterface $item)
    {
        $this->validate([$item->attribute => $item->value], [$item->attribute => $item->rules]);
    }

    public function replaceAttributes(AttributeCollection $attributes)
    {
        $this->attributes = $attributes->toArray();
    }

    public function setValidationRules(array $rules)
    {
        $rules = new ValidationRules($rules);

        $this->items->each(function ($item, $key) use ($rules) {
            if (! is_string($key) && is_a($item, ComponentAttributeInterface::class, true)) {
                $key = $item::getAttributeName();
            }
            if (is_string($rules->for($key) ?? [])) {
                dd($key, $rules);
            }
            $item->setRules($rules->for($key) ?? []);
        });
    }

    public function validate($attributes = null, $rules = null)
    {
        $attributes = $attributes ?? $this->toArray();

        $rules = $rules ?? $this->rules;

        $validator = Validator::make($attributes, $rules)->stopOnFirstFailure();

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
    }

    public function getItems()
    {
        return $this->items;
    }

    public function addItem($attribute, $value)
    {
        // attribute = stack, name, etc
        // value = string/function etc
        $item = $this->hasAttribute($attribute) ? $this->items->get($attribute)->setValue($value) : new BackpackAttribute($attribute, $value);

        $item->validate($value);
        dump($attribute, $value, $item);
        $this->updateItem($item);
    }

    public function addCollectionItem(BackpackComponentInterface $item)
    {
        $this->collectionRepository->addItem($item->getAttribute('name'), $item->getAttributesArray());
    }

    private function updateItem(ComponentAttributeInterface|BackpackAttribute $item)
    {
        $this->items[$item->attribute] = $item;
        $this->collectionRepository->setItemAttributes($this->getAttributeValue('name'), $this->toArray());
    }

    public function deleteItem(string $name)
    {
        $this->items->forget($name);
        $this->collectionRepository->removeItem($name);
    }

    public function toArrayWithDefaults()
    {
        return $this->items->mapWithKeys(function ($item, $key) {
            return [$key => $item->value];
        })->toArray();
    }

    public function toArray()
    {
        return $this->items->mapWithKeys(function ($item, $key) {
            return [$key => $item->value];
        })->toArray();
    }

    public function setAttributeDefaults(array $defaults)
    {
        foreach ($defaults as $key => $default) {
            if (! is_string($key) && is_a($default, ComponentAttributeInterface::class, true)) {
                $key = $default::getAttributeName();
            }

            if (! $this->items->has($key)) {
                if (is_a($default, ComponentAttributeInterface::class, true)) {
                    $this->items[$key] = $default::make($key, $default::getDefault($this), $default, []);
                    continue;
                }

                if ($default instanceof \Closure) {
                    $this->items[$key] = new BackpackAttribute($key, $default($this), $default []);
                    continue;
                }

                $this->items[$key] = new BackpackAttribute($key, $default, $default, []);
            }
        }
    }
}
