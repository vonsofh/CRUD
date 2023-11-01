<?php

namespace Backpack\CRUD\app\Library\Uploaders\Support;
use Backpack\CRUD\app\Library\Uploaders\Support\Interfaces\UploaderInterface;
use Exception;

final class UploaderLocator
{
    public static function for(array $crudObject, array $uploaderConfiguration, string $crudObjectType, string $macro): UploaderInterface
    {
        if (isset($uploaderConfiguration['uploader']) && class_exists($uploaderConfiguration['uploader'])) {
            return $uploaderConfiguration['uploader']::for($crudObject, $uploaderConfiguration);
        }
        
        if (app('UploadersRepository')->hasUploadFor($crudObject['type'], $macro)) {
            return app('UploadersRepository')->getUploadFor($crudObject['type'], $macro)::for($crudObject, $uploaderConfiguration);
        }

        throw new Exception('Undefined upload type for '.$crudObjectType.' type: '.$crudObject['type']);
    }
}