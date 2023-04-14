<?php

namespace Backpack\CRUD\app\Library\Uploaders\Support\Interfaces;

use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

interface FileNameGeneratorInterface
{
    public function getName(string|UploadedFile|File $file): string;
}
