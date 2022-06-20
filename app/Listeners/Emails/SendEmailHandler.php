<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Mail\TransactionalEmail;
use App\Models\CustomEmail;
use App\Models\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class SendEmailHandler {

    protected ?string $templateName;
    protected string $toEmail;
    protected object $event;
    protected ?string $type = null;

    protected function sendCustomEmail(): void {
        $attachmentName = null;
        try {
            $emailDataQuery = CustomEmail::where('name', $this->templateName);
            if ($this->type !== null) {
                $emailDataQuery->where('user_type', $this->type);
            }
            $emailData = $emailDataQuery->first();

            if ($emailData) {
                Mail::send(new TransactionalEmail($emailData, new EmailVariables($this->event), $this->toEmail));
                Log::channel('emails')->info('Email success: ', [
                    'template'   => $this->templateName,
                    'event_name' => get_class($this->event),
                    'user_email' => $this->toEmail,
                    'message'    => 'Sent',
                ]);
            } else {
                throw new \Exception('Email template #' . $this->templateName . ' was not found');
            }
        } catch (\Exception $e) {
            Log::channel('emails')->error('Email error: ', [
                'template'   => $this->templateName,
                'event_name' => get_class($this->event),
                'user_email' => $this->toEmail,
                'message'    => $e->getMessage().':'.$e->getFile().':'.$e->getLine(),
            ]);
        } finally {
            if ($attachmentName !== null) {
                Storage::disk('local')->delete($attachmentName);
            }
        }
    }

}
