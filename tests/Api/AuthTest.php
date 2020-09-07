<?php

namespace Tests\Api;

use App\Actions\CreateApplication;
use App\Models\Application;
use App\Models\ApplicationUser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_user_can_register_a_new_account(): void
    {
        $user    = factory(User::class)->make();
        $payload = [
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'email'      => $user->email,
            'password'   => '12345678'
        ];

        $response = $this->json('post', '/api/auth/register', $payload);
        $response->assertOk();
    }

    public function test_user_can_get_his_profile(): void
    {
        $response = $this->json('get', '/api/auth/profile');
        $response->assertOk();
    }
    public function test_user_can_update_his_profile(): void
    {
        $newUser = factory(User::class)->make();
        $response = $this->json('put', "/api/auth/profile",
            [
                'first_name' => $newUser->first_name,
                'last_name' => $newUser->last_name,
            ]);
        $response->assertOk()
            ->assertJson([
                'first_name' => $newUser->first_name,
                'last_name' => $newUser->last_name,
            ]);
    }
    public function test_can_avatar(){
        Storage::fake('avatars');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->json('POST', '/api/auth/profile/avatar', [
            'avatar' => $file,
        ]);
        Storage::disk('avatars')->assertExists($file->hashName());
        //Storage::disk('avatars')->assertMissing('missing.jpg');
    }
    public function test_can_background(){
        Storage::fake('avatars');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->json('POST', '/api/auth/profile/avatar', [
            'avatar' => $file,
        ]);
        Storage::disk('avatars')->assertExists($file->hashName());
        //Storage::disk('avatars')->assertMissing('missing.jpg');
    }

    public function test_user_can_log_in(): void
    {
        $user    = factory(User::class)->create(['password' => Hash::make('12345678')]);
        $payload = ['email' => $user->email, 'password' => '12345678'];

        $response = $this->json('post', '/api/auth/login', $payload);
        $response->assertOk();
    }

    public function test_user_can_ask_for_password_reset(): void
    {
        $payload = ['email' => $this->user->email];

        $response = $this->json('post', '/api/auth/forgot-password-ask', $payload);
        $response->assertOk();
    }

    public function test_user_can_claim_password_reset(): void
    {
        DB::table('password_resets')->insert([
            'email' => $this->user->email,
            'token' => 123,
        ]);
        $payload = [
            'email'    => $this->user->email,
            'token'    => 123,
            'password' => '9999999'
        ];

        $response = $this->json('post', '/api/auth/forgot-password-claim', $payload);
        $response->assertStatus(204);

        //check that user can login with a new password
        $this->json('post', '/api/auth/login', ['email' => $this->user->email, 'password' => '9999999'])
            ->assertJsonStructure(['access_token' => ['token']])
            ->assertOk();
    }
}
