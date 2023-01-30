<?php

namespace Backpack\CRUD\app\Http\Controllers\Operations;

use Backpack\CRUD\app\Exceptions\BackpackProRequiredException;

if (! backpack_pro()) {
    trait DropzoneOperation
    {
        public function setupDropzoneOperationDefaults()
        {
            throw new BackpackProRequiredException('DropzoneOperation');
        }
    }
} else {
    trait DropzoneOperation
    {
        use \Backpack\Pro\Http\Controllers\Operations\DropzoneOperation;
    }
}
