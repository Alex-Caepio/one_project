<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\ContractualServiceUpdateDeclinedBookingCancelled;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class ContractualServiceUpdateDeclinedBookingCancelledEmail extends SendEmailHandler {

    protected string $templateName = 'Contractual Service Update Declined - Booking Cancelled';

    public function handle(ContractualServiceUpdateDeclinedBookingCancelled $event): void {
        $this->toEmail = $event->client->email;
        $this->event->recipient = $event->client;
        $this->sendCustomEmail();
    }
}
