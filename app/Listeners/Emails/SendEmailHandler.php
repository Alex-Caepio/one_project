<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
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
                $emailVariables = new EmailVariables($this->event);
                $bodyReplaced = $this->wrapTemplate($emailData, $emailVariables);

                Mail::html($bodyReplaced, function($message) use ($emailData, $emailVariables) {
                    $subjectReplaced = $emailVariables->replace($emailData->subject);
                    $message->to($this->toEmail)->subject($subjectReplaced)
                            ->from($emailData->from_email, $emailData->from_title);

                    //Attach calendar file
                    if ($emailVariables->calendarPresented === true && $this->event->schedule instanceof Schedule) {
                        $attachmentName = $emailVariables->generateIcs($this->event->schedule);
                        $message->attach(storage_path('local').DIRECTORY_SEPARATOR.$attachmentName, [
                            'as'   => $attachmentName,
                            'mime' => 'text/calendar',
                        ]);
                    }
                });
            } else {
                throw new \Exception('Email template #' . $this->templateName . ' was not found');
            }
        } catch (\Exception $e) {
            Log::channel('emails')->info('Email error: ', [
                'template'   => $this->templateName,
                'event_name' => get_class($this->event),
                'user_email' => $this->toEmail,
                'message'    => $e->getMessage(),
            ]);
        } finally {
            if ($attachmentName !== null) {
                Storage::disk('local')->delete($attachmentName);
            }
        }
    }


    /**
     * @param \App\Models\CustomEmail $emailData
     * @param \App\EmailVariables\EmailVariables $emailVariables
     * @return string
     */
    private function wrapTemplate(CustomEmail $emailData, EmailVariables $emailVariables): string {
        $subject = $emailVariables->replace($emailData->subject);
        return '<!DOCTYPE html><html><head><title>' . $subject . '</title></head><body><table><tr>
        <td><img src="' . $emailData->logo . '" alt="' . $subject . '" /></td></tr>'
               . $emailVariables->replace($emailData->text) . '</table></body></html>';
    }


}
