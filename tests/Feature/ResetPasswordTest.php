<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User
     *
     * User who is to be used in the tests.
     */
    protected $user;

    /** @var string
     *
     * The old password that is to be used in the tests.
     */
    protected $oldPassword = 'password';

    /** @var string
     *
     * The new password that is to be used in the tests.
     */
    protected $newPassword = 'password123';

    public function setUp(): void
    {
        parent::setUp(); // Required to include the TestCase set up code as well.

        $this->user = User::factory()->create(); // New user is created who will be used in the tests.
    }

    /**
     * @test
     *
     * Test to see if a user can reset their password.
     *
     * @return void
     */
    public function canResetPassword()
    {
        $token = Password::broker()->createToken($this->user);

        $response = $this->postJson($this->apiBasePrefix . '/reset-password', [
            'email' => $this->user->email,
            'password' => $this->newPassword,
            'password_confirmation' => $this->newPassword,
            'token' => $token,
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(200);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'Your password has been reset!',
        ]);

        // Check that the user's password has updated.
        $this->user->refresh();
        $this->assertTrue(Hash::check($this->newPassword, $this->user->password));
    }

    /**
     * @test
     *
     * Test to see if a password isn't reset if the
     * supplied new password and the confirm password
     * don't match.
     *
     * @return void
     */
    public function cannotResetPasswordIfPasswordsDontMatch()
    {
        $token = Password::broker()->createToken($this->user);

        $response = $this->postJson($this->apiBasePrefix . '/reset-password', [
            'email' => $this->user->email,
            'password' => $this->newPassword,
            'password_confirmation' => $this->newPassword . 'isWrong',
            'token' => $token,
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(422);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'password' => [
                    'The password confirmation does not match.'
                ]
            ]
        ]);

        // Check that the user's password hasn't changed.
        $this->user->refresh();
        $this->assertTrue(Hash::check($this->oldPassword, $this->user->password));
    }

    /**
     * @test
     *
     * Test to see if a password isn't reset if the
     * email token value is invalid.
     *
     * @return void
     */
    public function cannotResetPasswordWithInvalidEmailToken()
    {
        $response = $this->postJson($this->apiBasePrefix . '/reset-password', [
            'email' => $this->user->email,
            'password' => $this->newPassword,
            'password_confirmation' => $this->newPassword,
            'token' => 'invalid_token',
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(422);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => [
                    'This password reset token is invalid.'
                ]
            ]
        ]);

        // Check that the user's password hasn't changed.
        $this->user->refresh();
        $this->assertTrue(Hash::check($this->oldPassword, $this->user->password));
    }

    /**
     * @test
     *
     * Test to see if a password isn't reset if the
     * email token value is missing from the request
     * form.
     *
     * @return void
     */
    public function cannotResetPasswordWithoutEmailToken()
    {
        $response = $this->postJson($this->apiBasePrefix . '/reset-password', [
            'email' => $this->user->email,
            'password' => $this->newPassword,
            'password_confirmation' => $this->newPassword,
            'token' => '',
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(422);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'token' => [
                    'The token field is required.'
                ]
            ]
        ]);

        // Check that the user's password hasn't changed.
        $this->user->refresh();
        $this->assertTrue(Hash::check($this->oldPassword, $this->user->password));
    }
}
