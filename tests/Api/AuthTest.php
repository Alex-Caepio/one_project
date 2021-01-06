<?php

namespace Tests\Api;

use App\Actions\CreateApplication;
use App\Models\Application;
use App\Models\ApplicationUser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
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

    public function test_user_publish(): void
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->json('post', 'api/auth/profile/publish', [
            'is_published' => true
        ]);
        $response->assertOk();
    }

    public function test_user_can_register_a_new_account(): void
    {
        Event::fake();
        $payload = [
            'first_name'              => 'John',
            'last_name'               => 'Doe',
            'email'                   => 'test12@test.com',
            'password'                => '12342_kLfasbfk',
            'account_type'            => 'client',
            'emails_holistify_update' => true,
            'accepted_terms'          => true
        ];

        $response = $this->json('post', '/api/auth/register', $payload);

        $response->assertOk();
    }

    public function test_user_can_get_his_profile(): void
    {
        $response = $this->json('get', '/api/auth/profile');
        $response->assertOk();
    }

    public function test_user_can_update_his_password(): void
    {
        Event::fake();
        $user = User::factory()->create();
        // 1. User provided correct current password and a new password
        $response = $this->actingAs($this->user)->json('put', "/api/auth/profile",
            [
                'token' => $user->token,
                'current_password' => 'test',
                'password'         => 'newPassword1',
            ]);
        $response->assertOk();
        $this->assertTrue(
            Hash::check('newPassword1', $this->user->password),
            'Password was not updated'
        );

        // 2. User did not provided current password
        $response = $this->json('put', "/api/auth/profile",
            [
                'password' => 'newPassword2',
            ]);
        $response->assertStatus(422)->assertJsonStructure(['errors'=>['current_password']]);
        $this->assertTrue(
            Hash::check('newPassword1', $this->user->password),
            'Password was not updated'
        );

        // 3. User provided wrong current password
        $response = $this->json('put', "/api/auth/profile",
            [
                'current_password' => 'wrong',
                'password' => 'newPassword3',
            ]);
        $response->assertStatus(422)->assertJsonStructure(['errors'=>['current_password']]);
        $this->assertTrue(
            Hash::check('newPassword1', $this->user->password),
            'Password was not updated'
        );
    }

    public function test_user_update_password_logic(): void
    {
        $newUser = User::factory()->make();

        $response = $this->json('put', "/api/auth/profile",
            [
                'first_name' => $newUser->first_name,
                'last_name'  => $newUser->last_name,
            ]);
        $response->assertOk()
            ->assertJson([
                'first_name' => $newUser->first_name,
                'last_name'  => $newUser->last_name,
            ]);
    }

    public function test_can_upload_avatar()
    {
        $user     = User::factory()->make();
        $path     = public_path('\img\profile\\' . $user->id . '\\');
        $file     = UploadedFile::fake()->image('avatar.jpg');
        $fileName = $file->getClientOriginalName();
        $this->json('post', '/api/auth/profile/avatar', [
            'avatar' => $file,
        ]);
        Storage::files($path, $fileName);
    }

    public function test_can_upload_background()
    {
        $user     = User::factory()->make();
        $path     = public_path('\img\profile\\' . $user->id . '\\');
        $file     = UploadedFile::fake()->image('background.jpg');
        $fileName = $file->getClientOriginalName();
        $this->json('post', '/api/auth/profile/background', [
            'background' => $file,
        ]);
        Storage::files($path, $fileName);
    }

    public function test_user_can_log_in(): void
    {
        $user    = User::factory()->create(['password' => Hash::make('12345678')]);
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
            'password' => '1123aaaFg'
        ];

        $response = $this->json('post', '/api/auth/forgot-password-claim', $payload);
        $response->assertOk()
            ->assertJsonStructure(['access_token' => ['token']]);

        //check that user can login with a new password
        $response = $this->json('post', '/api/auth/login', ['email' => $this->user->email, 'password' => '1123aaaFg'])
            ->assertJsonStructure(['access_token' => ['token']])
            ->assertOk();
    }
}
