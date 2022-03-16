<?php

namespace Backpack\CRUD\Tests\Unit\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Backpack\CRUD\app\Models\Traits\SpatieTranslatable\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class TranslatableModel extends Model
{
    use CrudTrait, HasTranslations;

    protected $table = 'translatables';
    protected $fillable = ['translatable_field', 'translatable_fake', 'translatable_field_with_mutator'];

    protected $fakeColumns = ['translatable_fake'];

    protected $translatable = ['translatable_field', 'translatable_fake', 'translatable_field_with_mutator'];

    public $timestamps = false;

    public function setTranslatableFieldWithMutatorAttribute($value)
    {
        $this->attributes['translatable_field_with_mutator'] = ! is_array($value) ?
                                                                        strtoupper($value) :
                                                                        array_map(function ($item) {
                                                                            return strtoupper($item);
                                                                        }, (array) $value);
    }
}
