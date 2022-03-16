<?php

namespace Backpack\CRUD\Tests\Unit\CrudPanel;

use Backpack\CRUD\Tests\Unit\Models\TranslatableModel;

class CrudPanelTrannslatableTest extends BaseDBCrudPanelTest
{
    private $translatableFields = [
        [
            'name' => 'translatable_field',
        ],
        [
            'name' => 'fake_1',
            'fake' => true,
            'store_in' => 'translatable_fake',
        ],
        [
            'name' => 'fake_2',
            'fake' => true,
            'store_in' => 'translatable_fake',
        ],
        [
            'name' => 'translatable_field_with_mutator',
        ],
    ];

    private $input_en = [
        'translatable_field' => 'field en',
        'fake_1' => 'fake en',
        'fake_2' => 'fake2 en',
        'translatable_field_with_mutator' => 'field mutator en',
    ];

    private $input_fr = [
        'translatable_field' => 'field fr',
        'fake_1' => 'fake fr',
        'fake_2' => 'fake2 fr',
        'translatable_field_with_mutator' => 'field mutator fr',
    ];

    public function testManuallyCreateModelTranslations()
    {
        config()->set('backpack.crud.locales', ['en' => 'English', 'fr' => 'French']);

        $model = TranslatableModel::create([
            'translatable_field' => ['en' => 'test en', 'fr' => 'test fr'],
            'translatable_fake' => 'test',
            'translatable_field_with_mutator' => 'test',
        ]);

        $this->assertEquals('test en', $model->translatable_field);
        $model->setLocale('fr');
        $this->assertEquals('test fr', $model->translatable_field);
    }

    public function testCreateAndUpdateTranslatableFields()
    {
        config()->set('backpack.crud.locales', ['en' => 'English', 'fr' => 'French']);

        $this->crudPanel->setModel(TranslatableModel::class);
        $this->crudPanel->addFields($this->translatableFields);

        $model = $this->crudPanel->create($this->input_en);
        $model = $model->withFakes();

        $this->assertEquals('field en', $model->translatable_field);
        $this->assertEquals('fake en', $model->fake_1);
        $this->assertEquals('fake2 en', $model->fake_2);
        $this->assertEquals('FIELD MUTATOR EN', $model->translatable_field_with_mutator);

        app()->setLocale('fr');

        $model = $this->crudPanel->update($model->id, $this->input_fr);
        $model->withFakes();

        $this->assertEquals('field fr', $model->translatable_field);
        $this->assertEquals('fake fr', $model->fake_1);
        $this->assertEquals('fake2 fr', $model->fake_2);
        $this->assertEquals('FIELD MUTATOR FR', $model->translatable_field_with_mutator);

        $model->setLocale('en');
        $this->assertEquals('field en', $model->translatable_field);
    }

    public function testPassArrayIntoTranslatableWithoutExplicitTranslation()
    {
        config()->set('backpack.crud.locales', ['en' => 'English', 'fr' => 'French']);

        $model = TranslatableModel::create([
            'translatable_field' => ['some_key' => 'some_value'],
            'translatable_fake' => ['some_key' => 'some_value'],
            'translatable_field_with_mutator' => ['some_key' => 'some_value'],
        ]);

        $this->assertEquals(['some_key' => 'some_value'], json_decode($model->translatable_field, true));
        $this->assertEquals(['some_key' => 'some_value'], json_decode($model->translatable_fake, true));
        $this->assertEquals(['some_key' => 'SOME_VALUE'], json_decode($model->translatable_field_with_mutator, true));
    }
}
