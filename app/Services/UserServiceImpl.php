<?php

namespace App\Services;

use App\Filters\UserFilter;
use App\Models\User;
use Carbon\Carbon;

class UserServiceImpl extends GenericServiceImpl implements IUserService
{
    public function __construct()
    {
        parent::__construct(new User(), new UserFilter());
    }

    public function createToken(User $user): string
    {
        $expireAt = Carbon::now()->addDays(config('auth.api.days_expire_token'));
        return $user->createToken(
            $user->email,
            [],
            $expireAt
        )->plainTextToken;
    }
}
