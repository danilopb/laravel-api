<?php

namespace App\Helpers;

class ExceptionHelper
{
    public static function getValuesErrorLog(\Exception $e): array
    {
        return [
            'message_error' => $e->getMessage(),
            'error_code' => $e->getCode(),
            'file_error' => $e->getFile(),
            'line_error' => $e->getLine(),
        ];
    }
}
