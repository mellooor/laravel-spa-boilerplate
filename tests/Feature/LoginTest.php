<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User
     *
     * User who is to be used in the tests.
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp(); // Required to include the TestCase set up code as well.

        $this->user = User::factory()->create(); // New user is created who will be used in the tests.
    }

    /**
     * @test
     *
     * Test to see if a user can successfully log in.
     *
     * @return void
     */
    public function userCanLoginWithCorrectCredentials()
    {
        $response = $this->postJson($this->apiBasePrefix . '/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(200);
    }

    /**
     * @test
     *
     * Test to see if  a user cannot log in with invalid credentials.
     */
    public function userCannotLoginWithIncorrectCredentials()
    {
        $response = $this->postJson($this->apiBasePrefix . '/login', [
            'email' => $this->user->email,
            'password' => 'wrong_password',
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(422);

        // Check that the correct JSON content is returned.
        $response->assertJson([
           'message' => 'The given data was invalid.',
           'errors' => [
               'email' => [ 'These credentials do not match our records.' ]
           ]
        ]);
    }
}
