<?php

namespace Tests\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }

    public function test_all_client(): void
    {
        User::factory()->count(2)->create(['account_type' => 'client']);
        $response = $this->json('get', "/admin/clients");

        $response
            ->assertOk()->assertJsonStructure([
                ['id', 'first_name', 'last_name', 'email'],
            ]);
    }

    public function test_store_client(): void
    {
        $user = User::factory()->make();
        $payload = ['first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'is_admin' => true,
            'email' => $user->email,
            'password' => $user->password,
            'account_type' => 'client'];
        $response = $this->json('post', "/admin/clients", $payload);

        $response->assertOk();
    }

    public function test_show_client(): void
    {
        $client = User::factory()->make(['account_type' => 'client']);
        $response = $this->json('get', "/admin/clients/{$client->id}");

        $response->assertOk();
    }

    public function test_update_admin(): void
    {
        $user = User::factory()->create(['account_type' => 'client']);
        $response = $this->json('put', "admin/clients/{$user->id}",
            [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'password' => $user->password,
            ]);

        $response->assertOk();
    }

    public function test_delete_client(): void
    {
        $client = User::factory()->create(['account_type' => 'client']);
        $response = $this->json('delete', "/admin/clients/{$client->id}");

        $response->assertStatus(204);
    }

}
