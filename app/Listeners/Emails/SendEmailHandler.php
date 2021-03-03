<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

abstract class SendEmailHandler {

    protected string $templateName;
    protected string $toEmail;
    protected object $event;
    protected ?string $type;

    protected function sendCustomEmail(): void {
        try {
            $emailDataQuery = CustomEmail::where('name', $this->templateName);
            if ($this->type !== null) {
                $emailDataQuery->where('user_type', $this->type);
            }
            $emailData = $emailDataQuery->first();
            if ($emailData) {
                $body = $emailData->text;
                $emailVariables = new EmailVariables($this->event);
                $bodyReplaced = '<html>'.$emailVariables->replace($body).'</html>';
                Mail::html($bodyReplaced, function($message) use($emailData) {
                    $message->to($this->toEmail)
                            ->subject($emailData->subject)
                            ->from($emailData->from_email, $emailData->from_title);
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
