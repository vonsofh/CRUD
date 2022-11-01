<?php

namespace Backpack\CRUD\app\Models\Traits\SpatieTranslatable;

use Spatie\Translatable\HasTranslations as OriginalHasTranslations;

trait HasTranslations
{
    use OriginalHasTranslations;

    /**
     * @var bool
     */
    public $locale = false;

    /*
    |--------------------------------------------------------------------------
    |                 SPATIE/LARAVEL-TRANSLATABLE OVERWRITES
    |--------------------------------------------------------------------------
    */

    /**
     * Use the forced locale if present.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        if (! $this->isTranslatableAttribute($key)) {
            return parent::getAttributeValue($key);
        }

        $translation = $this->getTranslation($key, $this->locale ?: app()->getLocale());

        // if it's a fake field, json_encode it
        if (is_array($translation)) {
            return json_encode($translation, JSON_UNESCAPED_UNICODE);
        }

        return $translation;
    }

    public function setAttribute($key, $value)
    {
        if ($this->isTranslatableAttribute($key) && is_array($value)) {
            // if it is a fake column, set the whole column translation
            if ($this->isFakeColumn($key)) {
                return $this->setTranslation($key, $this->getLocale(), $value);
            }

            // if none of the array keys match an available translation, translate the whole array
            $possibleTranslations = array_keys($value);
            $translatableLocales = array_keys($this->getAvailableLocales());

            if (! array_intersect($translatableLocales, $possibleTranslations)) {
                return $this->setTranslation($key, $this->getLocale(), $value);
            }

            // otherwise assume developer provided the attribute translations. eg: ['name' => ['en' => 'name' => 'pt' => 'nome']]
            return $this->setTranslations($key, $value);
        }

        // Pass arrays and untranslatable attributes to the parent method.
        if (! $this->isTranslatableAttribute($key) || is_array($value)) {
            return parent::setAttribute($key, $value);
        }

        // If the attribute is translatable and not already translated, set a
        // translation for the current app locale.
        return $this->setTranslation($key, $this->getLocale(), $value);
    }

    public function getTranslation(string $key, string $locale, bool $useFallbackLocale = true)
    {
        $locale = $this->normalizeLocale($key, $locale, $useFallbackLocale);

        $translations = $this->getTranslations($key);

        $translation = $translations[$locale] ?? '';

        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $translation);
        }

        return $translation;
    }

    /*
    |--------------------------------------------------------------------------
    |                            CUSTOM METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if a model is translatable, by the adapter's standards.
     *
     * @return bool
     */
    public function translationEnabledForModel()
    {
        return property_exists($this, 'translatable');
    }

    /**
     * Get all locales the admin is allowed to use.
     *
     * @return array
     */
    public function getAvailableLocales()
    {
        return config('backpack.crud.locales');
    }

    /**
     * Set the locale property. Used in normalizeLocale() to force the translation
     * to a different language that the one set in app()->getLocale();.
     *
     * @param string
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get the locale property. Used in SpatieTranslatableSluggableService
     * to save the slug for the appropriate language.
     *
     * @param string
     */
    public function getLocale()
    {
        if ($this->locale) {
            return $this->locale;
        }

        return \Request::input('_locale', \App::getLocale());
    }

    /**
     * Magic method to get the db entries already translated in the wanted locale.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return
     */
    public function __call($method, $parameters)
    {
        switch ($method) {
            // translate all find methods
            case 'find':
            case 'findOrFail':
            case 'findMany':
            case 'findBySlug':
            case 'findBySlugOrFail':
                $translation_locale = \Request::input('_locale', \App::getLocale());

                if ($translation_locale) {
                    $item = parent::__call($method, $parameters);

                    if ($item instanceof \Traversable) {
                        foreach ($item as $instance) {
                            $instance->setLocale($translation_locale);
                        }
                    } elseif ($item) {
                        $item->setLocale($translation_locale);
                    }

                    return $item;
                }

                return parent::__call($method, $parameters);
                break;

                // do not translate any other methods
            default:
                return parent::__call($method, $parameters);
                break;
        }
    }
}
