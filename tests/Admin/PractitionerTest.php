<?php

namespace Tests\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PractitionerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }

    public function test_all_practitioner(): void
    {
        User::factory()->count(2)->create(['account_type' => 'practitioner']);
        $response = $this->json('get', "/admin/practitioners");

        $response
            ->assertOk()->assertJsonStructure([
                ['id', 'first_name', 'last_name', 'email'],
            ]);
    }

    public function test_store_client(): void
    {
        $user = User::factory()->make(['account_type' => 'practitioner']);
        $payload = ['first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'is_admin' => true,
            'email' => $user->email,
            'password' => $user->password,
            'account_type' => 'client'];
        $response = $this->json('post', "/admin/practitioners", $payload);

        $response->assertOk();
    }

    public function test_show_practitioner(): void
    {
        $practitioner = User::factory()->make(['account_type' => 'practitioner']);
        $response = $this->json('get', "/admin/practitioners/{$practitioner->id}");

        $response->assertOk();
    }

    public function test_update_practitioner(): void
    {
        $practitioner = User::factory()->create(['account_type' => 'practitioner']);
        $response = $this->json('put', "admin/practitioners/{$practitioner->id}",
            [
                'first_name' => $practitioner->first_name,
                'last_name' => $practitioner->last_name,
                'email' => $practitioner->email,
                'password' => $practitioner->password,
            ]);

        $response->assertOk();
    }

    public function test_delete_practitioner(): void
    {
        $practitioner = User::factory()->create(['account_type' => 'practitioner']);
        $response = $this->json('delete', "/admin/practitioners/{$practitioner->id}");

        $response->assertStatus(204);
    }
}