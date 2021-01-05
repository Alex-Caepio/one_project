<?php

namespace Tests\Admin;


use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }

    /**
     * @skip
     */
    public function test_all_admin(): void
    {
        User::factory()->count(2)->create();
        $response = $this->json('get', "/admin/admins");

        $response
            ->assertOk()->assertJsonStructure([
                ['id', 'first_name', 'last_name', 'email'],
            ]);
    }

    /**
     * @skip
     */
    public function test_store_admin(): void
    {
        $user = User::factory()->make();
        $payload = ['first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'is_admin' => true,
            'email' => $user->email,
            'password' => $user->password,
            'account_type' => $user->account_type];
        $response = $this->json('post', "/admin/admins", $payload);

        $response->assertOk();
    }

    /**
     * @skip
     */
    public function test_show_admin(): void
    {
        $response = $this->json('get', "/admin/admins/{$this->user->id}");

        $response->assertOk();
    }

    /**
     * @skip
     */
    public function test_update_admin(): void
    {
        $user = User::factory()->create();
        $response = $this->json('put', "admin/profile",
            [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => 'gasgjasdj@adshas.com',
                'current_password' => $user->password,
                'password' => 'Qwerty1234',
            ]);

        $response->assertOk();
    }

    /**
     * @skip
     */
    public function test_get_profile_admin(): void
    {
        $response = $this->json('get', "admin/profile");
        $response->assertOk();
    }

    /**
     * @skip
     */
    public function test_delete_admin(): void
    {
        $response = $this->json('delete', "/admin/admins/{$this->user->id}");
        $response->assertStatus(204);
    }
}
