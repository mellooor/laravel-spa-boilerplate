<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
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
     * Test to see if a user can register an account successfully.
     *
     * @return void
     */
    public function canRegisterAccount()
    {
        $name = 'Example Examplerson';
        $email = 'example@example.com';
        $password = 'password';

        $response = $this->postJson($this->apiBasePrefix . '/register', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(201);

        // Check that the user has been added to the DB.
        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email
        ]);
    }

    /**
     * @test
     *
     * Test to see if an account cannot be created if the supplied email
     * address has previously been used to make an account.
     *
     * @return void
     */
    public function cannotRegisterAccountWithCredentialsThatMatchAnExistingAccount()
    {
        $response = $this->postJson($this->apiBasePrefix . '/register', [
            'email' => $this->user->email,
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password,
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(422);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => [ 'The email has already been taken.' ]
            ]
        ]);

        // Check that another user hasn't been added to the DB.
        $this->assertDatabaseCount('users', 1);
    }
}
