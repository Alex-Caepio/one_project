<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendUserMail extends Mailable {
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
     * Create a new message instance.
     *
     * @param User $sender
     * @param User $receiver
     * @param string $text
     */
    public function __construct(User $sender, User $receiver, string $text) {
        $this->text = $text;
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->subject = ($this->sender->isPractitioner() ? config('app.platform_subject_practitioner') : config('app.platform_subject_client'))
                         .$this->getSenderName();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->view('mails.send_user', [
            'text'     => $this->text,
            'sender'   => $this->sender,
            'receiver' => $this->receiver,
        ])->replyTo($this->sender->email, $this->getSenderName());
    }

    private function getSenderName(): string {
        return $this->sender->isPractitioner() ? $this->sender->business_name : $this->sender->first_name . ' ' .
                                                                                $this->sender->last_name;
    }

}
