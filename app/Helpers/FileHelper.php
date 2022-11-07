<?php

namespace App\Helpers;

class FileHelper
{
    public static function generateName(string $extension): string
    {
        return rand()."-".time().".".$extension;
    }
}
