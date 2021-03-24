<?php

namespace App\Mail;

use App\EmailVariables\EmailVariables;
use App\Models\CustomEmail;
use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionalEmail extends Mailable {
    use Queueable, SerializesModels;

    public CustomEmail $emailData;
    public EmailVariables $emailVariables;
    public string $recipient;
    public string $replacedSubject;
    public string $replacedContent;
    public ?string $logoContent;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\CustomEmail $emailData
     * @param \App\EmailVariables\EmailVariables $emailVariables
     */
    public function __construct(CustomEmail $emailData, EmailVariables $emailVariables, string $recipient) {
        $this->emailData = $emailData;
        $this->emailVariables = $emailVariables;
        $this->recipient = $recipient;
        $this->replacedSubject = $this->emailVariables->replace($this->emailData->subject);
        $this->replacedContent = $this->emailVariables->replace($this->emailData->text);
        $this->logoContent = $this->emailData->getEmbedImageContent();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->view('mails.custom_emails')->from($this->emailData->from_email, $this->emailData->from_title)
                    ->subject($this->replacedSubject)->to($this->recipient)->attachCalendar();
    }


    /**
     * @return $this
     */
    public function attachCalendar(): Mailable {
        $schedule = $this->emailVariables->getSchedule();
        if ($this->emailVariables->calendarPresented === true && $schedule instanceof Schedule) {
            $attachmentName = $this->emailVariables->generateIcs($schedule);
            $this->attach(storage_path('app') . DIRECTORY_SEPARATOR . $attachmentName, [
                'as'   => $attachmentName,
                'mime' => 'text/calendar',
            ]);
        }
        return $this;
    }


}
