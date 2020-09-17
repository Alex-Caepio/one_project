<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetLink extends Mailable
{
    use Queueable, SerializesModels;

    protected $link;

    /**
     * Create a new message instance.
     *
     * @param string $link
     */
    public function __construct(string $link)
    {
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this
            ->subject('Reset password')
            ->view('mails.password_reset_ask', ['link' => $this->link]);
    }
}
