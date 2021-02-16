<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\CustomEmailEvent;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

abstract class SendEmailHandler {

    protected string $templateName;
    protected string $toEmail;
    protected object $event;

    protected function sendCustomEmail(): void {
        try {
            $emailData = CustomEmail::where('name', $this->templateName)->first();
            if ($emailData) {
                $body = $emailData->text;
                $emailVariables = new EmailVariables($this->event);
                $bodyReplaced = $emailVariables->replace($body);
                Mail::html($bodyReplaced, function($message) {
                    $message->to($this->toEmail);
                });
            } else {
                throw new \RuntimeException('Email template #' . $this->templateName . ' was not found');
            }
        } catch (\Exception $e) {
            Log::channel('emails')->info('Email error: ', [
                'template'   => $this->templateName,
                'event_name' => get_class($this->event),
                'user_email' => $this->toEmail,
                'message'    => $e->getMessage(),
            ]);
        }
    }


}
