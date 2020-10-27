<?php


namespace App\EmailVariables;


use Carbon\Carbon;
use Illuminate\Support\Facades\URL;

class EmailVariables
{

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function __get($property)
    {
        $property = ucfirst("$property");
        $method = "get{$property}";
        return $this->$method();

    }

    public function replace($body)
    {
        $openBracket = strpos($body, '{{');
        if (!$openBracket) {
            return $body;
        }

        $closeBracket = strpos($body, '}}');
        $lengthVariable = $closeBracket - $openBracket - 2;
        $variable = trim(substr($body, $openBracket + 2, $lengthVariable));
        $length = $closeBracket - $openBracket + 2;

        $newBody = substr_replace($body, $this->$variable, $openBracket, $length);

        return $this->replace($newBody);
    }

    public function getFirst_name()
    {
        return $this->event->user->first_name;
    }

    public function getPlatform_name()
    {
        return 'Oneness';
    }

    public function getPractitioner_URL()
    {
        return $this->event->user->email;
    }

    public function getEmail_verification_url()
    {
        $linkApi = URL::temporarySignedRoute('verify-email', now()->addHours(48), [
            'user' => $this->event->user->id,
            'email' => $this->event->user->email
        ]);
        return config('app.frontend_email_confirm_page') . '?' . explode('?', $linkApi)[1];
    }

    public function getArticle_url()
    {
        return $this->event->article->url;
    }

    public function getArticle_name()
    {
        return $this->event->article->title;
    }

    public function getSubscription_tier_name()
    {
        return 'dsfsd';
    }

    public function getPlatform_email()
    {
        return 'asfsdfs';
    }

    public function getSubscription_cost()
    {
        return $this->event->schedule->cost;
    }

    public function getService_name()
    {
        return $this->event->service->title;
    }

    public function getService_url()
    {
        return $this->event->service->url;
    }

    public function getSchedule_name()
    {
        return $this->event->schedule->title;
    }

    public function getSchedule_start_date()
    {
        return Carbon::parse($this->event->schedule->start_date)->toDateString();
    }

    public function getSchedule_start_time()
    {
        return Carbon::parse($this->event->schedule->start_date)->toTimeString();
    }

    public function getSchedule_end_date()
    {
        return Carbon::parse($this->event->schedule->end_date)->toDateString();
    }

    public function getSchedule_end_time()
    {
        return Carbon::parse($this->event->schedule->end_date)->toTimeString();
    }

    public function getSchedule_hosting_url()
    {
        return "dsfsdafa";
    }

    public function getAdmin_termination_message()
    {
        return 'Administrator attribution notification';
    }

    public function getSchedule_city()
    {
        return $this->event->user->email;
    }

    public function getSchedule_venue_name()
    {
        return $this->event->user->email;
    }

    public function getSchedule_postcode()
    {
        return $this->event->user->email;
    }

    public function getSchedule_venue_address()
    {
        return $this->event->user->email;
    }

    public function getTotal_paid()
    {
        return $this->event->user->email;
    }

    public function getCancelled_start_date()
    {
        return $this->event->user->email;
    }

    public function getCancelled_start_time()
    {
        return $this->event->user->email;
    }

    public function getCancelled_end_date()
    {
        return $this->event->user->email;
    }

    public function getCancelled_end_time()
    {
        return $this->event->user->email;
    }

    public function getPractitioner_business_name()
    {
        return $this->event->user->email;
    }

    public function getVerify_email()
    {
        return $this->event->user->email;
    }

    public function getBooking_reference()
    {
        return $this->event->user->email;
    }

    public function getReschedule_start_date()
    {
        return $this->event->user->email;
    }

    public function getReschedule_start_time()
    {
        return $this->event->user->email;
    }

    public function getReschedule_end_date()
    {
        return $this->event->user->email;
    }

    public function getReschedule_end_time()
    {
        return $this->event->user->email;
    }

    public function getReschedule_venue_name()
    {
        return $this->event->user->email;
    }

    public function getService_schedule_reschedule_url()
    {
        return $this->event->user->email;
    }

    public function getReschedule_venue_address()
    {
        return $this->event->user->email;
    }

    public function getReschedule_city()
    {
        return $this->event->user->email;
    }

    public function getReschedule_postcode()
    {
        return $this->event->user->email;
    }

    public function getReschedule_country()
    {
        return $this->event->user->email;
    }
}
