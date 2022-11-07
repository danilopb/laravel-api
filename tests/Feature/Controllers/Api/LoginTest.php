<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * test login.
     *
     * @return void
     */
    public function test_login_with_valid_credentials()
    {
        $user = User::factory()
            ->create();
        $data = [
            'email' => $user->email,
            'password' => 'qwerty',
        ];
        $response = $this->post(route('login'), $data, $this->getApiHeader($this->token));
        $response->assertStatus(201);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login_with_invalid_password()
    {
        $user = User::factory()
            ->create();
        $data = [
            'email' => $user->email,
            'password' => 'invalid_password',
        ];
        $response = $this->post(route('login'), $data, $this->getApiHeader($this->token));
        $response->assertStatus(400);
    }
}
