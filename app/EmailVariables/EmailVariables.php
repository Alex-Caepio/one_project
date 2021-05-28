<?php


namespace App\EmailVariables;

use App\Events\BookingConfirmation;
use App\Models\Schedule;
use App\Models\User;
use App\Traits\GenerateCalendarLink;
use App\Traits\RescheduleEmailLinks;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;


/**
 * Class EmailVariables
 *
 * @package App\EmailVariables
 */
class EmailVariables {

    use GenerateCalendarLink, RescheduleEmailLinks;

    public function __construct($event) {
        $this->event = $event;
    }

    public function __get(string $property) {
        if (!empty($property)) {
            $property = ucfirst($property);
            $method = "get{$property}";
            return method_exists($this, $method) ? $this->$method() : '';
        }
    }

    public function replace($body) {
        $openBracket = strpos($body, '{{');
        if ($openBracket === false) {
            return $body;
        }

        $closeBracket = strpos($body, '}}');
        $lengthVariable = $closeBracket - $openBracket - 2;
        $variable = trim(substr($body, $openBracket + 2, $lengthVariable));
        $length = $closeBracket - $openBracket + 2;
        if (!empty($variable)) {
            $newBody = substr_replace($body, $this->$variable, $openBracket, $length);
            return $this->replace($newBody);
        }

        return '';
    }


    /**
     * @return \App\Models\Schedule|null
     */
    public function getSchedule(): ?Schedule {
        return $this->event->schedule ?? null;
    }


    /**
     * @return string
     */
    public function getPlatform_name(): string {
        return config('app.platform_name');
    }

    /**
     *
     *
     * USER VARIABLES START
     *
     *
     */

    /**
     * @return string
     */
    public function getFirst_name(): string {
        return $this->event->recipient->first_name ?? $this->event->user->first_name;
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
    public function getPlatform_email(): string {
        return config('app.platform_email');
    }


    /**
     * @return string
     */
    public function getReset_password_url(): string {
        return config('app.frontend_reset_password_form_url') . '?token=' . $this->event->reset->token;
    }

    /**
     * @return string
     */
    public function getMy_account(): string {
        return config('app.frontend_account_link');
    }

    /**
     * @return string
     */
    public function getMy_profile(): string {
        return config('app.frontend_profile_link');
    }

    /**
     * @return string
     */
    public function getPractitioner_Url(): ?string {
        return config('app.frontend_public_profile').$this->event->user->slug;
    }

    /**
     * @return \App\Models\User|null
     */
    public function getPractitioner(): ?User {
        return $this->event->practitioner ?? null;
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
    public function getPractitioner_business_name(): ?string {
        return $this->event->user->isPractitioner() ? $this->event->user->business_name : $this->event->practitioner->business_name;
    }

    /**
     * @return string
     */
    public function getPractitioner_email_address(): string {
        return $this->event->user->isPractitioner() ? $this->event->user->email : $this->event->practitioner->email;
    }

    /**
     * @return string
     */
    public function getAdmin_termination_message(): ?string {
        return $this->event->user->termination_message;
    }

    /**
     *
     *
     * USER VARIABLES END
     *
     *
     */


    /**
     *
     *
     * SERVICE VARIABLES START
     *
     *
     */


    /**
     * @return string
     */
    public function getService_name(): ?string {
        return $this->event->service->title;
    }

    /**
     * @return string
     */
    public function getService_url(): ?string {
        return $this->event->service->url;
    }


    /**
     * @return string
     */
    public function getAdd_to_calendar(): ?string {
        $this->calendarPresented = true;
        return $this->generateGoogleLink($this->event->schedule);
    }

    /**
     *
     *
     * SERVICE VARIABLES END
     *
     *
     */


    /**
     *
     *
     * SCHEDULE VARIABLES START
     *
     *
     */

    /**
     * @return string
     */
    public function getSchedule_name(): ?string {
        return $this->event->schedule->title;
    }

    /**
     * @return string
     */
    public function getSchedule_start_date(): string {
        return $this->event->schedule->start_date ? Carbon::parse($this->event->schedule->start_date)
                                                          ->toDateString() : '';
    }

    /**
     * @return string
     */
    public function getSchedule_start_time(): string {
        return $this->event->schedule->start_date ? Carbon::parse($this->event->schedule->start_date)
                                                          ->toTimeString() : '';
    }

    /**
     * @return string
     */
    public function getSchedule_end_date(): string {
        return $this->event->schedule->end_date ? Carbon::parse($this->event->schedule->end_date)->toDateString() : '';
    }

    /**
     * @return string
     */
    public function getSchedule_end_time(): string {
        return $this->event->schedule->end_date ? Carbon::parse($this->event->schedule->end_date)->toTimeString() : '';
    }

    /**
     * @return string
     */
    public function getSchedule_venue_name(): ?string {
        return $this->event->schedule->venue_name;
    }

    /**
     * @return string
     */
    public function getSchedule_venue_address(): ?string {
        return $this->event->schedule->venue_address;
    }

    /**
     * @return string
     */
    public function getSchedule_city(): ?string {
        return $this->event->schedule->city;
    }

    /**
     * @return string
     */

    public function getSchedule_country(): ?string {
        return $this->event->schedule->country;
    }

    /**
     * @return string
     */
    public function getSchedule_postcode(): ?string {
        return $this->event->schedule->post_code;
    }

    /**
     * @return string
     */
    public function getSchedule_hosting_url(): ?string {
        return $this->event->schedule->url;
    }

    /**
     * @return string
     */
    public function getPractitioner_schedule_message(): ?string {
        return $this->event->schedule->comments;
    }

    /**
     *
     *
     * SCHEDULE VARIABLES END
     *
     *
     */

    /**
     *
     *
     * SUBSCRIPTION VARIABLES START
     *
     *
     */

    /**
     * @return string
     */
    public function getSubscription_tier_name(): string {
        return $this->event->plan->name;
    }

    /**
     * @return string
     */
    public function getSubscription_cost(): string {
        return number_format($this->event->plan->price, 2);
    }

    /**
     * @return string
     */
    public function getSubscription_start_date(): string {
        return Carbon::parse($this->event->user->plan_from)->format('d.m.Y');
    }

    /**
     * @return string
     */
    public function getSubscription_end_date(): string {
        return Carbon::parse($this->event->user->plan_until)->format('d.m.Y');
    }

    /**
     *
     *
     * SUBSCRIPTION VARIABLES START
     *
     *
     */

    /**
     *
     *
     * ARTICLES VARIABLES START
     *
     *
     */

    /**
     * @return string
     */
    public function getMy_articles(): ?string {
        return config('app.frontend_practitioner_services');
    }

    /**
     * @return string
     */
    public function getArticle_url(): ?string {
        return $this->event->article->url;
    }

    /**
     * @return string
     */
    public function getArticle_name(): ?string {
        return $this->event->article->title;
    }

    /**
     *
     *
     * ARTICLES VARIABLES END
     *
     *
     */

    /**
     *
     *
     * BOOKINGS VARIABLES START
     *
     *
     */

    /**
     * @return string
     */
    public function getBooking_reference(): string {
        return $this->event->booking->reference;
    }

    /**
     * @return float
     */
    public function getTotal_paid(): float {
        return (float)$this->event->booking->cost;
    }

    /**
     * @return integer
     */
    public function getTickets_count(): int {
        return (int)$this->event->booking->amount;
    }

    /**
     * @return string
     */
    public function getClient_name(): string {
        return $this->event->client->first_name . ' ' . $this->event->client->last_name;
    }


    /**
     * @return string
     */
    public function getClient_email_address(): string {
        return $this->event->client->email ?? '';
    }

    /**
     * @return string
     */
    public function getSee_on_map(): string {
        $addressCollection = array_filter([
            $this->event->schedule->venue_name,
            $this->event->schedule->venue_address,
            $this->event->schedule->city,
            $this->event->schedule->country,
            $this->event->schedule->post_code
        ], static function(?string $value) {
            if (!empty($value)) {
                return str_replace(' ', '+', trim($value));
            }
        });

        return 'https://www.google.com/maps/search/?api=1&map_action=map&query=' . implode(', ', $addressCollection);
    }

    /**
     * @return string
     */
    public function getAccept(): string {
        return config('app.frontend_reschedule_apply') . '/' . $this->event->reschedule->id
               . '?token='.$this->generatePersonalAccessToken($this->event->client);
    }

    /**
     * @return string
     */
    public function getDecline(): string {
        return config('app.frontend_reschedule_decline') . '/' . $this->event->reschedule->id
               . '?token='.$this->generatePersonalAccessToken($this->event->client, 'decline');
    }

    /**
     * @return string
     */
    public function getView_booking(): string {
        return config('app.frontend_booking_url') . $this->event->booking->id;
    }

    /**
     * @return string
     */
    public function getView_my_booking(): string {
        return $this->getView_booking();
    }

    /**
     * @return string
     */
    public function getRebook(): string {
        return config('app.frontend_booking_url') . $this->event->booking->id;
    }

    /**
     * @return string
     */
    public function getView_bookings(): string {
        return config('app.frontend_booking_url');
    }


    /**
     *
     *
     * BOOKING VARIABLES END
     *
     *
     */

    /**
     * RESCHEDULES START
     *
     */

    /**
     * @return string
     */
    public function getReschedule_start_date(): string {
        return isset($this->event->reschedule_schedule) && $this->event->reschedule_schedule->start_date
            ? Carbon::parse($this->event->reschedule_schedule->start_date)->toDateString() : '';
    }

    /**
     * @return string
     */
    public function getReschedule_start_time(): string {
        return isset($this->event->reschedule_schedule) && $this->event->reschedule_schedule->start_date
            ? Carbon::parse($this->event->reschedule_schedule->start_date)->toTimeString() : '';
    }

    /**
     * @return string
     */
    public function getReschedule_end_date(): string {
        return isset($this->event->reschedule_schedule) && $this->event->reschedule_schedule->end_date
            ? Carbon::parse($this->event->reschedule_schedule->end_date)->toDateString() : '';
    }

    /**
     * @return string
     */
    public function getReschedule_end_time(): string {
        return isset($this->event->reschedule_schedule) && $this->event->reschedule_schedule->end_date
            ? Carbon::parse($this->event->reschedule_schedule->end_date)->toTimeString() : '';
    }

    /**
     * @return string
     */
    public function getReschedule_venue_name(): ?string {
        return isset($this->event->reschedule_schedule) ? $this->event->reschedule_schedule->venue_name : '';
    }

    /**
     * @return string
     */
    public function getReschedule_venue_address(): ?string {
        return isset($this->event->reschedule_schedule) ? $this->event->reschedule_schedule->venue_address : '';
    }

    /**
     * @return string
     */
    public function getReschedule_city(): ?string {
        return isset($this->event->reschedule_schedule) ? $this->event->reschedule_schedule->city : '';
    }

    /**
     * @return string
     */
    public function getReschedule_country(): ?string {
        return isset($this->event->reschedule_schedule) ? $this->event->reschedule_schedule->country : '';
    }

    /**
     * @return string
     */
    public function getReschedule_postcode(): ?string {
        return isset($this->event->reschedule_schedule) ? $this->event->reschedule_schedule->post_code : '';
    }

    /**
     * @return string
     */
    public function getReschedule_hosting_url(): ?string {
        return isset($this->event->reschedule_schedule) ? $this->event->reschedule_schedule->url : '';
    }

    /**
     * @return string
     */
    public function getPractitioner_reschedule_message(): ?string {
        return $this->event instanceof BookingConfirmation
            ? $this->event->schedule->comments
            : $this->event->reschedule_schedule->comment;
    }

    /**
     * @return string
     */
    public function getPractitioner_message(): ?string {
        return $this->event instanceof BookingConfirmation ? $this->event->schedule->comments : '';
    }

    /**
     * RESCHEDULES END
     *
     */


    /**
     * @return string
     */
    public function getInstalments(): string {
        return '';
    }


}
