<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordHasBeenChanged extends Mailable
{
    use Queueable, SerializesModels;

    protected $link;

    /**
     * Create a new message instance.
     *
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Reset password')->view('mails.password_has_been_changed');
    }
}
