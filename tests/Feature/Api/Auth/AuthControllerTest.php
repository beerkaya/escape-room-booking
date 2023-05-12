<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void
    {
        parent::setUp();

        // fake all notifications that are sent out during tests
        Notification::fake();

        // create a user
        User::factory()->create([
            'email' => 'johndoe@example.org',
            'password' => Hash::make('testpassword'),
            'date_of_birth' => Carbon::now()->addYears(-30)->format('Y-m-d'),
        ]);

    }

    public function test_register_show_validation_error_when_both_fields_empty()
    {
        $response = $this->json('POST', route('auth.register'), [
            'email' => '',
            'password' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }


    public function test_register_successful()
    {
        $response = $this->json('POST', route('auth.register'), [
            'name' => 'Test Test',
            'email' => 'test@test.com',
            'password' => 'abcdabcd'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'message', 'user', 'token']);
    }

    public function test_login_show_validation_error_when_both_fields_empty()
    {
        $response = $this->json('POST', route('auth.login'), [
            'email' => '',
            'password' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }


    public function test_login_show_authentication_error_when_credential_donot_match()
    {
        $response = $this->json('POST', route('auth.login'), [
            'email' => 'test@test.com',
            'password' => 'abcdabcd'
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure(['status', 'message']);
    }

    public function test_login_return_user_and_access_token_after_successful_login()
    {
        $response = $this->json('POST', route('auth.login'), [
            'email' =>'johndoe@example.org',
            'password' => 'testpassword',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'message', 'user', 'token']);
    }
}
