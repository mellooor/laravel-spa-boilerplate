<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
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
     * Test to see if a user can trigger the forgot
     * password email.
     *
     * @return void
     */
    public function userCanTriggerForgotPasswordEmail()
    {
        $response = $this->postJson($this->apiBasePrefix . '/forgot-password', [
            'email' => $this->user->email,
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(200);

        // Check that the correct JSON content is returned.
        $response->assertJson([
           'message' => 'We have emailed your password reset link!',
        ]);

        // Check that te password reset token has been added to the DB.
        $this->assertDatabaseHas('password_resets', [
           'email' => $this->user->email
        ]);
    }

    /**
     * @test
     *
     * Test to see if a user cannot trigger the forgot
     * password email if the supplied email address
     * doesn't match to any accounts in the system.
     *
     * @return void
     */
    public function userCannotTriggerForgotPasswordEmailWithInvalidEmail()
    {
        $response = $this->postJson($this->apiBasePrefix . '/forgot-password', [
            'email' => 'invalid@email.com',
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(422);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => [
                    'We can\'t find a user with that email address.'
                ]
            ]
        ]);

        // Check that nothing has been added to the password_resets table in the DB.
        $this->assertDatabaseMissing('password_resets', [
           'email' => $this->user->email
        ]);
    }
}
