<?php

namespace Backpack\CRUD\app\Library\Validation\Rules;

use Backpack\CRUD\app\Library\Validation\Rules\Support\ValidateArrayContract;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

abstract class BackpackCustomRule implements ValidationRule, DataAwareRule, ValidatorAwareRule
{
    use Support\HasFiles;

    /**
     * @var \Illuminate\Contracts\Validation\Validator
     */
    protected $validator;

    protected array $data;

    public array $fieldRules = [];

    public bool $implicit = true;

    public static function field(string|array|ValidationRule|Rule $rules = []): self
    {
        $instance = new static();
        $instance->fieldRules = self::getRulesAsArray($rules);

        if ($instance->validatesArrays()) {
            if (! in_array('array', $instance->getFieldRules())) {
                $instance->fieldRules[] = 'array';
            }
        }

        return $instance;
    }

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
        $value = $this->ensureValueIsValid($value);

        if ($value === false) {
            $fail('Invalid value for the attribute.')->translate();

            return;
        }

        $errors = $this->validateOnSubmit($attribute, $value);
        foreach ($errors as $error) {
            $fail($error)->translate();
        }
    }

    /**
     * Set the performing validator.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return $this
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function getFieldRules(): array
    {
        return tap($this->fieldRules, function ($rule) {
            if (is_a($rule, BackpackCustomRule::class, true)) {
                $rule = $rule->getFieldRules();
            }

            return $rule;
        });
    }

    protected static function getRulesAsArray($rules)
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        if (! is_array($rules)) {
            $rules = [$rules];
        }

        return $rules;
    }

    protected function ensureValueIsValid($value)
    {
        if ($this->validatesArrays() && ! is_array($value)) {
            try {
                $value = json_decode($value, true) ?? [];
            } catch(\Exception $e) {
                return false;
            }
        }

        return $value;
    }

    private function validatesArrays(): bool
    {
        return is_a($this, ValidateArrayContract::class);
    }

    private function validateAndGetErrors(string $attribute, mixed $value, array $rules): array
    {
        $validator = Validator::make($value, [
            $attribute => $rules,
        ], $this->validator->customMessages, $this->validator->customAttributes);

        return $validator->errors()->messages()[$attribute] ?? (! empty($validator->errors()->messages()) ? current($validator->errors()->messages()) : []);
    }

    protected function getValidationAttributeString(string $attribute)
    {
        return Str::substrCount($attribute, '.') > 1 ?
                Str::before($attribute, '.').'.*.'.Str::afterLast($attribute, '.') :
                $attribute;
    }

    protected function validateOnSubmit(string $attribute, mixed $value): array
    {
        return $this->validateRules($attribute, $value);
    }

    protected function validateFieldAndFile(string $attribute, null|array $data = null, array|null $customRules = null): array
    {
        $fieldErrors = $this->validateFieldRules($attribute, $data, $customRules);

        $fileErrors = $this->validateFileRules($attribute, $data);

        return array_merge($fieldErrors, $fileErrors);
    }

    /**
     * Implementation.
     */
    public function validateFieldRules(string $attribute, null|array|UploadedFile $data = null, array|null $customRules = null): array
    {
        $data = $data ?? $this->data;
        $validationRuleAttribute = $this->getValidationAttributeString($attribute);
        $data = $this->prepareValidatorData($data, $attribute);

        return $this->validateAndGetErrors($validationRuleAttribute, $data, $customRules ?? $this->getFieldRules());
    }

    protected function prepareValidatorData(array|UploadedFile $data, string $attribute): array
    {
        if ($this->validatesArrays() && is_array($data) && ! Str::contains($attribute, '.')) {
            return Arr::has($data, $attribute) ? $data : [$attribute => Arr::get($data, $attribute)];
        }

        if (Str::contains($attribute, '.')) {
            $validData = [];

            Arr::set($validData, $attribute, ! is_array($data) ? $data : Arr::get($data, $attribute));

            return $validData;
        }

        return [$attribute => $data];
    }

    protected function validateFileRules(string $attribute, mixed $data): array
    {
        $items = $this->prepareValidatorData($data ?? $this->data, $attribute);
        $items = is_array($items) ? $items : [$items];
        $validationRuleAttribute = $this->getValidationAttributeString($attribute);

        $filesToValidate = array_filter(Arr::get($items, $attribute), function ($item) {
            return $item instanceof UploadedFile;
        });

        Arr::set($items, $attribute, $filesToValidate);

        $errors = [];

        foreach ($filesToValidate as $key => $file) {
            $fileToValidate = [];
            Arr::set($fileToValidate, $attribute, $file);
            $errors[] = $this->validateAndGetErrors($validationRuleAttribute, $fileToValidate, $this->getFileRules());
        }

        return array_unique(array_merge(...$errors));
    }

    public function validateRules(string $attribute, mixed $value): array
    {
        return $this->validateFieldAndFile($attribute, $value);
    }
}
