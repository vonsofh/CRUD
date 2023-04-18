<?php

namespace Backpack\CRUD\app\Library\Uploaders\Support\Validation;
use Illuminate\Validation\Rules\File;

class UploadedFile extends File
{
    public ?int $maxFiles = null;
    public ?int $minFiles = null;

    public function maxFiles(int $maxFiles): self
    {
        $this->maxFiles = $maxFiles;

        return $this;
    }

    public function minFiles(int $minFiles): self
    {
        $this->minFiles = $minFiles;

        return $this;
    }
}