<?php


namespace App\EmailVariables;


use Carbon\Carbon;
use Illuminate\Support\Facades\URL;

class EmailVariables {

    public function __construct($event) {
        $this->event = $event;
    }

    public function __get(string $property) {
        $property = ucfirst($property);
        $method = "get{$property}";
        return $this->$method();
    }

    public function replace($body) {
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

    /**
     * @return string
     */
    public function getPlatform_name(): string {
        return config('app.platform_name');
    }

    /**
     * @return string
     */
    public function getFirst_name(): string {
        return $this->event->user->first_name;
    }

    /**
     * @return string
     */
    public function getEmail_verification_url(): string {
        $linkApi = URL::temporarySignedRoute('verify-email', now()->addHours(48), [
            'user'  => $this->event->user->id,
            'email' => $this->event->user->email
        ]);
        return config('app.frontend_email_confirm_page') . '?' . explode('?', $linkApi)[1];
    }


    /**
     * @return string
     */
    public function getPlatform_email():string {
        return config('app.platform_email');
    }


    /**
     * @return string
     */
    public function getReset_password_url(): string {
        return config('app.frontend_reset_password_form_url').'?token='.$this->event->reset->token;
    }

    /**
     * @return string
     */
    public function getSubscription_tier_name(): string {
        return $this->event->plan->name;
    }

    /**
     * @return string
     */
    public function getMy_account(): string {
        return config('app.frontend_profile_link');
    }

    /**
     * @return string
     */
    public function getPractitioner_URL(): string {
        return $this->event->user->public_link;
    }

    /**
     * @return string
     */
    public function getMy_services(): string {
        return config('app.frontend_practitioner_services');
    }

    /**
     * @return string
     */
    public function getPractitioner_business_name(): string {
        return $this->event->user->isPractitioner()
            ? $this->event->user->business_name
            : $this->event->practitioner->business_name;
    }




    /**
     * @return string
     */
    public function getSubscription_cost(): string {
        return number_format($this->event->plan->price, 2);
    }

    /**
     * @return string
     * Will be reworked to the new field or new model PlanPurchases
     */
    public function getSubscription_start_date(): string {
        return Carbon::now()->format('d.m.Y');
    }

    /**
     * @return string
     */
    public function getSubscription_end_date(): string {
        return Carbon::parse($this->event->user->plan_until)->format('d.m.Y');
    }

    /**
     * @return string
     */
    public function getMy_articles(): string {
        return config('app.frontend_practitioner_services');
    }

    /**
     * @return string
     */
    public function getService_name(): string {
        return $this->event->service->title;
    }

    /**
     * @return string
     */
    public function getService_url(): string {
        return $this->event->service->url;
    }

    /**
     * @return string
     */
    public function getAdd_to_calendar(): string {
        return '';
    }

    /**
     * @return string
     */
    public function getSchedule_name(): string {
        return $this->event->schedule->title;
    }

    /**
     * @return string
     */
    public function getSchedule_start_date(): string {
        return Carbon::parse($this->event->schedule->start_date)->toDateString();
    }

    /**
     * @return string
     */
    public function getSchedule_start_time(): string {
        return Carbon::parse($this->event->schedule->start_date)->toTimeString();
    }

    /**
     * @return string
     */
    public function getSchedule_end_date(): string {
        return Carbon::parse($this->event->schedule->end_date)->toDateString();
    }

    /**
     * @return string
     */
    public function getSchedule_end_time(): string {
        return Carbon::parse($this->event->schedule->end_date)->toTimeString();
    }

    /**
     * @return string
     */
    public function getSchedule_venue(): string {
        return $this->event->schedule->venue;
    }

    /**
     * @return string
     */
    public function getSchedule_city(): string {
        return $this->event->schedule->city;
    }

    /**
     * @return string
     */
    public function getSchedule_country(): string {
        return $this->event->schedule->country;
    }

    /**
     * @return string
     */
    public function getSchedule_postcode(): string {
        return $this->event->schedule->post_code;
    }

    /**
     * @return string
     */
    public function getSchedule_hosting_url(): string {
        return $this->event->schedule->url;
    }

    /**
     * @return string
     */
    public function getArticle_url(): string {
        return $this->event->article->url;
    }

    /**
     * @return string
     */
    public function getArticle_name(): string {
        return $this->event->article->title;
    }

    /**
     * @return string
     */
    public function getBooking_reference(): string {
        return $this->event->booking->reference;
    }

    /**
     * @return string
     */
    public function getView_my_booking(): string {
        return config('app.frontend_booking_url').$this->event->booking->reference;
    }

    /**
     * @return string
     */
    public function getView_my_bookings(): string {
        return config('app.frontend_booking_url');
    }

    /**
     * @return string
     */
    public function getInstalments(): string {
        $result = '';
        foreach ($this->event->installments as $instalment) {

        }
        return $result;
    }














    public function getAdmin_termination_message() {
        return 'Administrator attribution notification';
    }

    public function getTotal_paid() {
        return $this->event->user->email;
    }

    public function getCancelled_start_date() {
        return $this->event->user->email;
    }

    public function getCancelled_start_time() {
        return $this->event->user->email;
    }

    public function getCancelled_end_date() {
        return $this->event->user->email;
    }

    public function getCancelled_end_time() {
        return $this->event->user->email;
    }

    public function getReschedule_start_date() {
        return $this->event->user->email;
    }

    public function getReschedule_start_time() {
        return $this->event->user->email;
    }

    public function getReschedule_end_date() {
        return $this->event->user->email;
    }

    public function getReschedule_end_time() {
        return $this->event->user->email;
    }

    public function getReschedule_venue_name() {
        return $this->event->user->email;
    }

    public function getService_schedule_reschedule_url() {
        return $this->event->user->email;
    }

    public function getReschedule_venue_address() {
        return $this->event->user->email;
    }

    public function getReschedule_city() {
        return $this->event->user->email;
    }

    public function getReschedule_postcode() {
        return $this->event->user->email;
    }

    public function getReschedule_country() {
        return $this->event->user->email;
    }
}
