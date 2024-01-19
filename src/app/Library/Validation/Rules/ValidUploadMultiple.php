<?php

namespace Backpack\CRUD\app\Library\Validation\Rules;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade;
use Backpack\CRUD\app\Library\Uploaders\Uploader;
use Closure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        $entry = CrudPanelFacade::getCurrentEntry() ?: null;

        if (Str::contains($attribute, '.')) {
            $this->validateUploadInSubfield($attribute, $value, $fail, $entry);

            return;
        }

        if (! $value = self::ensureValidValue($value)) {
            $fail('Unable to determine the value type.');

            return;
        }

        // `upload_multiple` sends [[0 => null]] when user doesn't upload anything
        // assume that nothing changed on field so nothing is sent on the request.
        if (count($value) === 1 && empty($value[0])) {
            if ($entry) {
                unset($this->data[$attribute]);
            } else {
                $this->data[$attribute] = [];
            }
            $value = [];
        }

        $previousValues = $entry?->{$attribute} ?? [];
        if (is_string($previousValues)) {
            $previousValues = json_decode($previousValues, true) ?? [];
        }

        $value = array_merge($previousValues, $value);

        if ($entry) {
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

    protected function validateUploadInSubfield($attribute, $value, Closure $fail, $entry = null)
    {
        $mainField = Str::before($attribute, '.');
        $subfield = Str::afterLast($attribute, '.');
        $row = (int) Str::before(Str::after($attribute, '.'), '.');

        $values[$mainField] = Uploader::mergeFilesAndValuesRecursive($this->data[$mainField], $this->data['_order_'.$mainField] ?? []);

        if (! array_key_exists($subfield, $values[$mainField][$row]) && $entry) {
            return;
        }

        $this->createValidator($subfield, $this->getFieldRules(), $values[$mainField][$row][$subfield] ?? null, $fail);

        if (! empty($value) && ! empty($this->getFileRules())) {
            $this->createValidator($subfield, $this->getFileRules(), $values[$mainField][$row][$subfield] ?? null, $fail);
        }
    }

    protected function createValidator(string $attribute, array $rules, mixed $value, Closure $fail): void
    {
        $validator = Validator::make([$attribute => $value], [
            $attribute => $rules,
        ], $this->validator->customMessages, $this->validator->customAttributes);

        if ($validator->fails()) {
            foreach ($validator->errors()->messages()[$attribute] as $message) {
                $fail($message)->translate();
            }
        }
    }
}
