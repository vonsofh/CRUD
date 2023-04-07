<?php

namespace Backpack\CRUD\app\Library\CrudPanel\Uploads\Uploaders;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Backpack\CRUD\app\Library\CrudPanel\Uploads\Uploaders\Uploader;

class AjaxUploader extends Uploader
{
    public static function for(array $field, $configuration)
    {
        return (new self($field, $configuration))->multiple();
    }

    public function uploadRepeatableFile(Model $entry, $values = null)
    {
        dd($values);
    }

    public function uploadFile(Model $entry, $value = null)
    {
        $uploads = $value ?? CrudPanelFacade::getRequest()->input($this->name);

        // this should check if the field is casted
        if(!is_array($uploads)) {
            $uploads = json_decode($uploads, true);
        }
        // we are receiving duplicate files in request, that need to be fixed
        $uploads = array_unique($uploads);

        $temp_disk = config('backpack.base.temp_disk_name') ?? 'public';
        $temp_folder = config('backpack.base.temp_upload_folder_name') ?? 'backpack/temp';
        $updated_files = [];

        // Check if some fields were deleted and delete from disk
        if ($uploads != ($entry->getOriginal('dropzone') ?? [])) {
            $deleted = array_diff(($entry->getOriginal('dropzone') ?? []), $uploads);
            foreach ($deleted as $key => $value) {
                Storage::disk($temp_disk)->delete($value);
            }
        }

        foreach ($uploads as $key => $value) {
            if(!empty($value) && strpos($value, $temp_folder) !== false) {
                // If the file was uploaded and entity submitted
                try {
                    $name = substr($value, strrpos($value, '/') + 1);
                    $move = Storage::disk($this->disk)->move($value, $this->path . $name);

                    if($move) {
                        $value = str_replace($temp_folder, $this->path, $value);
                        $updated_files[] = $value;
                    }
                } catch (\Throwable $th) {
                    dd($th);
                }
            } else {
                // If the file was not changed
                $updated_files[] = $value;
            }

            // check if we need to json encode or not.
            return json_encode($updated_files);
        }
    }
}
