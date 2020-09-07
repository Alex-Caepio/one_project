<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $emailVerification;

    /**
     * Create a new message instance.
     *
     * @param $emailVerification
     */
    public function __construct($emailVerification)
    {
        //$this->link = $link;
        $this->emailVerification = $emailVerification;
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
            ->view('mails.verify_email', ['emailVerification' => $this->emailVerification]);
    }
}
