<?php

namespace App\Listeners\Emails;

use App\EmailVariables\EmailVariables;
use App\Events\ContractualServiceUpdateDeclinedBookingCancelled;
use App\Models\CustomEmail;
use Illuminate\Support\Facades\Mail;

class ContractualServiceUpdateDeclinedBookingCancelledEmail
{
    public function __construct()
    {
    }

    public function handle(ContractualServiceUpdateDeclinedBookingCancelled $event): void
    {
        $user = $event->user;
        $emailVerification = CustomEmail::where('name', 'Contractual Service Update Declined - Booking Cancelled')->first();
        $body = $emailVerification->text;
        $emailVariables = new EmailVariables($event);
        $bodyReplaced = $emailVariables->replace($body);

        Mail::raw($bodyReplaced, function ($message) use ($user){
            $message->to($user->email);
        });
    }
}
