<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $emailVerification,$link;

    /**
     * Create a new message instance.
     *
     * @param $emailVerification
     * @param string $link
     */
    public function __construct($emailVerification,string $link)
    {
        $this->link = $link;
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
            ->subject('Email Verification')
            ->view('mails.verify_email', ['emailVerification' => $this->emailVerification,'link' => $this->link]);
    }
}
