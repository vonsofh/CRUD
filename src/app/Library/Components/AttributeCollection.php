<?php

namespace Backpack\CRUD\app\Library\Components;

use Backpack\CRUD\app\Library\Components\Attributes\BaseAttribute;
use Backpack\CRUD\app\Library\Components\Interfaces\AttributeInterface;
use Backpack\CRUD\app\Library\Components\Interfaces\SmartComponentInterface;
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

        $defaults = collect($defaults)->mapWithKeys(function ($default, $attribute) {
            if (is_a($default, AttributeInterface::class, true)) {
                return [$default::getAttributeName() => $default];
            }

            return [$attribute => $default];
        })->toArray();

        $this->items = $this->buildAttributes($attributes, $rules, $defaults);

        assert($this->hasAttribute('name'), 'Component name cant be empty.');
        assert(is_string($this->getAttributeValue('name')), 'Component name must be a valid string.');
    }

    private function buildAttributes(Collection $attributes, ValidationRules $rules, array $defaults): Collection
    {
        return $attributes->mapWithKeys(function ($attribute, $key) use ($rules, $defaults) {
            if (! is_a($attribute, AttributeInterface::class, true)) {
                return [$key => new BaseAttribute($key, $attribute, $defaults[$key] ?? null, $rules->for($key))];
            }

            return [$attribute::getAttributeName() => $attribute];
        });
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
        return $this->items->has($attribute) ? $this->items->get($attribute, $default)->value() : $default;
    }

    public function getCollectionRepository()
    {
        return $this->collectionRepository;
    }

    public function replaceAttributes(AttributeCollection $attributes)
    {
        $this->attributes = $attributes->toArray();
    }

    public function setValidationRules(array $rules)
    {
        $rules = new ValidationRules($rules);

        $this->items->each(function ($item, $key) use ($rules) {
            if (! is_string($key) && is_a($item, AttributeInterface::class, true)) {
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

    public function addAttribute($attribute, $value)
    {
        $item = $this->hasAttribute($attribute) ? $this->items->get($attribute)->setValue($value) : new BaseAttribute($attribute, $value);
        $item->validate($value);
        $this->updateItem($item);
    }

    public function addCollectionOfAttributes(SmartComponentInterface $item)
    {
        $this->collectionRepository->addItem($item->getAttribute('name'), $item->getAttributesArray());
    }

    private function updateItem(AttributeInterface|BaseAttribute $item)
    {
        $this->items[$item->attribute] = $item;
        $this->collectionRepository->setItemAttributes($this->getAttributeValue('name'), $this->toArray());
    }

    public function deleteItem(string $name)
    {
        $this->items->forget($name);
        $this->collectionRepository->removeItem($name);
    }

    public function toArray()
    {
        return $this->items->mapWithKeys(function ($item, $key) {
            return [$key => $item->value()];
        })->toArray();
    }

    public function setAttributeDefaults(array $defaults)
    {
        foreach ($defaults as $key => $default) {
            if (! is_string($key) && is_a($default, AttributeInterface::class, true)) {
                $key = $default::getAttributeName();
            }

            if (! $this->items->has($key)) {
                if (is_a($default, AttributeInterface::class, true)) {
                    $this->items[$key] = $default::make($key, $default::getDefault($this), $default, []);

                    continue;
                }

                if ($default instanceof \Closure) {
                    $this->items[$key] = new BaseAttribute($key, $default($this), $default, []);

                    continue;
                }

                $this->items[$key] = new BaseAttribute($key, $default, $default, []);
            }
        }
    }
}
