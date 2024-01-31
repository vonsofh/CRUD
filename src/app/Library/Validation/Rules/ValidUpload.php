<?php

namespace Backpack\CRUD\app\Library\Validation\Rules;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade;
use Backpack\CRUD\app\Library\Uploaders\Uploader;
use Backpack\CRUD\app\Library\Validation\Rules\Support\HasFiles;
use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class ValidUpload extends BackpackCustomRule
{
    use HasFiles;

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $entry = CrudPanelFacade::getCurrentEntry();
        // check if string contains two dots
        if (Str::contains($attribute, '.') && Str::substrCount($attribute, '.') > 1) {
            $this->validateUploadInSubfield($attribute, $value, $fail, $entry);

            return;
        }

        if (! array_key_exists($attribute, $this->data) && $entry) {
            return;
        }

        $this->createValidator($attribute, $this->getFieldRules(), $value, $fail);

        if (! empty($value) && ! empty($this->getFileRules())) {
            $this->createValidator($attribute, $this->getFileRules(), $value, $fail);
        }
    }

    public static function field(string|array|ValidationRule|Rule $rules = []): self
    {
        return parent::field($rules);
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
}
