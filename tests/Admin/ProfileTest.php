<?php

namespace Tests\Admin;


use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }

    public function test_update_profile(): void
    {
        $user = User::factory()->make();
        $response = $this->json('put', 'admin/profile',
            [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ]);

        $response->assertOk();
    }

    public function test_admin_can_update_his_password(): void
    {
        Event::fake();
        // 1. User provided correct current password and a new password
        $response = $this->json('put', 'admin/profile',
            [
                'current_password' => '12dsfsdDDD',
                'password'         => 'newPassword1',
            ]);
        $response->assertOk();
        $this->assertTrue(
            Hash::check('newPassword1', $this->user->password),
            'Password was not updated'
        );

        // 2. User did not provided current password
        $response = $this->json('put', 'admin/profile',
            [
                'password' => 'newPassword2',
            ]);
        $response->assertStatus(422)->assertJsonStructure(['errors'=>['current_password']]);
        $this->assertTrue(
            Hash::check('newPassword1', $this->user->password),
            'Password was not updated'
        );

        // 3. User provided wrong current password
        $response = $this->json('put', 'admin/profile',
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


    public function test_get_profile_info(): void
    {
        $response = $this->json('get', 'admin/profile');
        $response->assertOk();
    }
}
