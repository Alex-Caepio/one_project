<?php

namespace Tests\Admin;


use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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
    public function test_all_admin(): void
    {
        User::factory()->count(2)->create()->where('is_admin', true);
        $response = $this->json('get', "/admin/admins");

        $response
            ->assertOk()->assertJsonStructure([
                ['id', 'first_name','last_name','email'],
            ]);
    }
    public function test_store_admin(): void
    {
        $user = User::factory()->count(2)->create()->where('is_admin', true);
        $payload = ['first_name' => $user->first_name,'last_name' => $user->last_name,'email' => $user->email];
        $response = $this->json('post', "/admin/admins",$payload);

        $response->assertOk();
    }
    public function test_show_admin(): void
    {
        $user = User::factory()->create()->where('is_admin', true);
        $response = $this->json('get', "/admin/admins/{$user->id}");

        $response
            ->assertOk();
    }
    public function test_update_admin(): void
    {
        $newUser= User::factory()->make()->where('is_admin', true);

        $response = $this->json('put', "admin/profile",
            [
                'first_name' => $newUser->first_name,
                'last_name' => $newUser->last_name,
                'email' => $newUser->email,
            ]);

        $response->assertOk();
    }
    public function test_delete_admin(): void
    {
        $user = User::factory()->create()->where('is_admin', true);
        $response = $this->json('delete', "/admin/admins/{$user->id}");

        $response->assertStatus(204);
    }
}
