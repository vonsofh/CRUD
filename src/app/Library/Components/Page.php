<?php

namespace Backpack\CRUD\app\Library\Components;

use Backpack\CRUD\app\Library\Components\Fields\Field;
use Backpack\CRUD\app\Library\Components\Interfaces\Components\SmartComponentInterface;

/**
 * @mixin  Backpack\CRUD\app\Library\Components\Field
 */
class Page
{
    public $backpackService;

    public function __construct()
    {
        $this->backpackService = app('crud');
    }

    private static function page()
    {
        return app('crud');
    }

    public static function button(string|array $name)
    {
    }

    public static function field(string|array|SmartComponentInterface $input)
    {
        $fieldRepository = new CollectionRepository(
            function () {
                return self::page()->getOperationSetting('fields') ?? collect();
            },
            function ($collection) {
                return self::page()->setOperationSetting('fields', $collection);
            }
        );

        if ($input instanceof SmartComponentInterface) {
            $attributes = new AttributeCollection($input->attributes(), $fieldRepository);

            return $input::makeOf($attributes);
        }

        $input = new AttributeCollection($input, $fieldRepository);

        return new Field($input);
    }
}
