<?php

namespace Tests;

use App\Models\User;
use App\Services\UserServiceImpl;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function getApiHeader($token): array
    {
        return ['Accept' => 'application/json', 'Authorization' => 'Bearer '.$token];
    }

    public function createUserByToken()
    {
        $user = User::factory()->create();
        $userService = new UserServiceImpl();
        $token = $userService->createToken($user);
        $user->withAccessToken($token);
        return $user;
    }
}
