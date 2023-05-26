<?php

namespace Backpack\CRUD\app\Http\Controllers\Operations\Support;

trait HasForm
{
    public function initFormElements()
    {
        $this->crud->mergeOperationConfig($this->crud->getCurrentOperation());

        $this->crud->setupDefaultSaveActions();

        $this->crud->setOperationSetting('breadcrumbs', [
            trans('backpack::crud.admin')   => url(config('backpack.base.route_prefix'), 'dashboard'),
            $this->crud->entity_name_plural => url($this->crud->route),
        ]);
    }

    public function formView()
    {
        $this->prepareFormData();

        return view($this->crud->getOperationSetting('view') ?? 'crud::form', $this->data);
    }

    public function setBreadcrumbs(string $operationKey, string|bool $operationLink = false): void
    {
        $this->crud->setOperationSetting('breadcrumbs',
            array_merge($this->crud->operationSetting('breadcrumbs') ?? [], [$operationKey => $operationLink])
        );
    }

    public function setDefaultHeadings($heading, $subheading)
    {
        $this->crud->setOperationSetting('heading', $heading);
        $this->crud->setOperationSetting('subheading', $subheading);
    }

    public function prepareFormData()
    {
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
    }
}
