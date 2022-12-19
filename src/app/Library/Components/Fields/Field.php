<?php

namespace Backpack\CRUD\app\Library\Components\Fields;

use Backpack\CRUD\app\Library\Components\AttributeCollection;
use Backpack\CRUD\app\Library\Components\Attributes\EntityAttribute;
use Backpack\CRUD\app\Library\Components\Attributes\Fields\FieldType;
use Backpack\CRUD\app\Library\Components\Attributes\LabelAttribute;
use Backpack\CRUD\app\Library\Components\Attributes\ModelAttribute;
use Backpack\CRUD\app\Library\Components\Attributes\RelationTypeAttribute;
use Backpack\CRUD\app\Library\Components\Component;

/**
 * @method self type()
 * @method self label()
 * @method self name()
 * @method self value()
 * @method self placeholder()
 * @method self hint()
 * @method self wrapper()
 */
class Field extends Component
{
    public function __construct(AttributeCollection $attributes)
    {
        parent::__construct($attributes);
    }

    /**
     * IMPORTANT: the order of the attributes is important. If you need some attribute that depends on another
     * attribute, make sure that the attribute that you need is defined before the attribute that depends on it.
     *
     * @return array
     */
    public static function getAttributes(): array
    {
        return [
            LabelAttribute::class,
            EntityAttribute::class,
            RelationTypeAttribute::class,
            FieldType::class,
            ModelAttribute::class,
        ];
    }
}
