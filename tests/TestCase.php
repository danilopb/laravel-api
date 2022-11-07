<?php

namespace Tests;

use App\Models\User;
use App\Services\UserServiceImpl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected User $user;
    protected string $token;
    protected string $fakeFileDriverName = "testing";

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserByToken();
        $this->token = $this->user->currentAccessToken();
    }

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
