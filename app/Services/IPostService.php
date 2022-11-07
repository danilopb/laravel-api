<?php

namespace App\Services;

interface IPostService
{
    public function existSlug($slug): bool;

    /**
     * Generate a unique slug
     * @param $title
     * @return string
     */
    public function generateUniqueSlug($title): string;
}
