<?php

namespace Backpack\CRUD\app\Library\Validation\Rules;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade;
use Closure;

class ValidUploadMultiple extends ValidFileArray
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value = self::ensureValidValue($value)) {
            $fail('Unable to determine the value type.');

            return;
        }

        // `upload_multiple` sends [[0 => null]] when user doesn't upload anything
        // assume that nothing changed on field so nothing is sent on the request.
        if ((is_countable($value) ? count($value) : 0) === 1 && empty($value[0])) {
            if ($this->entry) {
                unset($this->data[$attribute]);
            } else {
                $this->data[$attribute] = [];
            }
            $value = [];
        }

        $previousValues = $this->entry?->{$attribute} ?? [];
        if (is_string($previousValues)) {
            try {
                $previousValues = json_decode($previousValues, true, 512, JSON_THROW_ON_ERROR) ?? [];
            } catch (\JsonException $e) {
                $previousValues = [];
            }
        }

        $value = array_merge($previousValues, $value);

        if ($this->entry) {
            $filesDeleted = CrudPanelFacade::getRequest()->input('clear_'.$attribute) ?? [];

            $data = $this->data;
            $data[$attribute] = array_diff($value, $filesDeleted);

            $this->validateArrayData($attribute, $fail, $data);

            $this->validateItems($attribute, $value, $fail);

            return;
        }

        $this->validateArrayData($attribute, $fail);

        $this->validateItems($attribute, $value, $fail);
    }
}
