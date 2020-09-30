<?php

namespace Tests\Api;



use App\Models\EmailMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use DatabaseTransactions;
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }
    public function test_all_massage(): void
    {
      $g =  EmailMessage::factory()->count(2)->create();
        $response = $this->json('get', "/api/messages");

        $response
            ->assertOk()->assertJsonStructure([
                ['id', 'receiver_id','sender_id','text']
            ]);
    }
    public function test_create_message(): void
    {
        $user =User::factory()->create();
        $newUser = User::factory()->create();
        $email=EmailMessage::factory()->count(2)->create();
        $payload = ['receiver_id' => $user->receiver_id,'sender_id' => $newUser->sender_id,'text' => $email->text];
        $response = $this->json('post', "/api/messages/users/{$newUser->id}",$payload);

        $response
            ->assertOk();
    }
    public function test_show_message(): void
    {
        $user = User::factory()->create();
        $response = $this->json('get', "/api/messages/receiver/{$user->id}");

        $response
            ->assertOk();
    }
}
