<?php

namespace App\Services;

use App\Models\User;

interface IUserService
{
    public function createToken(User $user): string;
}
