<?php

namespace Backpack\CRUD\app\Library\Components\Fields;

use Backpack\CRUD\app\Library\Components\Attributes\EntityAttribute;
use Backpack\CRUD\app\Library\Components\Attributes\FieldType;
use Backpack\CRUD\app\Library\Components\AttributeCollection;
use Backpack\CRUD\app\Library\Components\ComponentCollection;
use Backpack\CRUD\app\Library\Components\Fields\Field;

class SelectFromModel extends Field
{
    public static function getBlockedAttributes(): array
    {
        return [FieldType::class];
    }

    public function model(string $model): self
    {
        $this->setAttribute('model', $model);

        return $this;
    }

    public static function getAttributeValidationRules(): array
    {
        return [
            'model' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (! is_a($value, 'Illuminate\Database\Eloquent\Model', true)) {
                        $fail('The '.$attribute.' should be a valid class that extends Illuminate\Database\Eloquent\Model .');
                    }
                },
            ],
            'attribute' => ['required', 'string'],
            EntityAttribute::class,
            'value'     => ['nullable', 'integer'],
        ];
    }

    public static function getAttributeDefaults(): array
    {
        return [
            EntityAttribute::class,
            'model'  => function ($attributes) {
                return app('BSL')->inferComponentModelFromRelationship($attributes->getAttributes());
            },
        ];
    }
}
