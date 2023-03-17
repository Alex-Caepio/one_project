<?php

namespace Tests\Admin;

use App\Models\CustomEmail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomEmailTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }

    public function test_all_custom_email(): void
    {
        CustomEmail::factory()->count(2)->create();
        $response = $this->json('get', "/admin/transactional-emails");

        $response
            ->assertOk()->assertJsonStructure([
                ['id', 'name', 'user_type', 'from_email', 'from_title', 'subject', 'logo', 'text', 'delay'],
            ]);
    }

    public function test_store_admin(): void
    {
        $customEmail = CustomEmail::factory()->make();
        $payload = [
            'name' => $customEmail->name,
            'user_type' => $customEmail->user_type,
            'from_email' => $this->user->email,
            'from_title' => $this->user->first_name . " " . $this->user->last_name,
            'subject' => $customEmail->subject,
            'logo' => $customEmail->logo,
            'text' => $customEmail->text,
            'delay' => $customEmail->delay,
        ];
        $response = $this->json('post', "/admin/transactional-emails", $payload);

        $response->assertOk();
    }
    public function test_show_custom_email(): void
    {
        $customEmail = CustomEmail::factory()->make();
        $response = $this->json('get', "/admin/transactional-emails/{$customEmail->id}");

        $response->assertOk();
    }

    public function test_update_custom_email(): void
    {
        $customEmail = CustomEmail::factory()->create();
        $response = $this->json('put', "admin/transactional-emails/{$customEmail->id}",
            [
                'name' => $customEmail->name,
                'user_type' => $customEmail->user_type,
                'subject' => $customEmail->subject,
                'logo' => $customEmail->logo,
                'text' => $customEmail->text,
                'delay' => $customEmail->delay,
            ]);

        $response->assertOk();
    }
    public function test_delete_custom_email(): void
    {
        $customEmail = CustomEmail::factory()->create();
        $response = $this->json('delete', "/admin/transactional-emails/{$customEmail->id}");

        $response->assertStatus(204);
    }
}
