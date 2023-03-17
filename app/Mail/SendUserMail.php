<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $text;
    /**
     * @var User
     */
    public $sender;
    /**
     * @var User
     */
    public $receiver;
    /**
     * @var string
     */
    private $conversationId;

    /**
     * Create a new message instance.
     *
     * @param User $sender
     * @param User $receiver
     * @param string $text
     */
    public function __construct(User $sender, User $receiver, string $text, string $conversationId)
    {
        $this->text = $text;
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->subject = ($this->sender->isPractitioner()
                ? config('app.platform_subject_practitioner')
                : config('app.platform_subject_client')) . $this->getSenderName();
        $this->conversationId = $conversationId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): SendUserMail
    {
        return $this
            ->view('mails.send_user', [
                'text' => $this->text,
                'sender' => $this->sender,
                'receiver' => $this->receiver,
                'replyLink' => $this->buildReplyLink(),
            ]);
    }

    /**
     * @return string
     */
    private function getSenderName(): string
    {
        return $this->sender->isPractitioner()
            ? $this->sender->business_name
            : $this->sender->first_name . ' ' . $this->sender->last_name;
    }

    /**
     * Generate url to conversation page with parameter
     *
     * @return string
     */
    private function buildReplyLink(): string
    {
        return config('app.frontend_url') . config('app.platform_conversation_url') . $this->conversationId;
    }

}
