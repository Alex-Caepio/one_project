<?php


namespace App\EmailVariables;

use App\Events\BookingConfirmation;
use App\Models\Instalment;
use App\Models\Price;
use App\Models\Schedule;
use App\Models\User;
use App\Traits\EventDates;
use App\Traits\GenerateCalendarLink;
use App\Traits\RescheduleEmailLinks;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;


/**
 * Class EmailVariables
 *
 * @package App\EmailVariables
 */
class EmailVariables
{

    use GenerateCalendarLink, RescheduleEmailLinks, EventDates;

    const TIME_FORMAT = 'H:i';
    const DATE_FORMAT = 'd-m-Y';
    private $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function __get(string $property)
    {
        if (!empty($property)) {
            $property = ucfirst($property);
            $method = "get{$property}";
            return method_exists($this, $method) ? $this->$method() : '';
        }
    }

    public function replace($body)
    {
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
     * @return Schedule|null
     */
    public function getSchedule(): ?Schedule
    {
        return $this->event->schedule ?? null;
    }


    /**
     * @return string
     */
    public function getPlatform_name(): string
    {
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
    public function getFirst_name(): string
    {
        return $this->event->recipient->first_name ?? $this->event->user->first_name;
    }

    /**
     * @return string
     */
    public function getEmail_verification_url(): string
    {
        $linkApi = URL::temporarySignedRoute('verify-email', now()->addHours(48), [
            'user' => $this->event->user->id,
            'email' => $this->event->user->email
        ]);
        return config('app.frontend_url') . config('app.frontend_email_confirm_page') . '?' . explode('?', $linkApi)[1];
    }


    /**
     * @return string
     */
    public function getPlatform_email(): string
    {
        return config('app.platform_email');
    }


    /**
     * @return string
     */
    public function getReset_password_url(): string
    {
        return config('app.frontend_url') . config('app.frontend_reset_password_form_url') . '?token=' . $this->event->reset->token;
    }

    /**
     * @return string
     */
    public function getMy_account(): string
    {
        return config('app.frontend_url') . config('app.frontend_account_link');
    }

    /**
     * @return string
     */
    public function getMy_profile(): string
    {
        return config('app.frontend_url') . config('app.frontend_profile_link');
    }

    /**
     * @return string
     */
    public function getPractitioner_Url(): ?string
    {
        return config('app.frontend_url') . str_replace(
                ['practitioner-slug'],
                [$this->event->user->slug],
                config('app.frontend_public_profile')
            );
    }

    /**
     * @return User|null
     */
    public function getPractitioner(): ?User
    {
        return $this->event->practitioner ?? null;
    }


    /**
     * @return string
     */
    public function getMy_services(): string
    {
        return config('app.frontend_url') . config('app.frontend_practitioner_services');
    }

    /**
     * @return string
     */
    public function getPractitioner_business_name(): ?string
    {
        return $this->event->user->isPractitioner()
            ? $this->event->user->business_name
            : $this->event->practitioner->business_name;
    }

    /**
     * @return string
     */
    public function getPractitioner_email_address(): string
    {
        return $this->event->user->isPractitioner()
            ? $this->event->user->email
            : $this->event->practitioner->email;
    }

    /**
     * @return string
     */
    public function getAdmin_termination_message(): ?string
    {
        return str_replace("\n", '<br>', $this->event->user->termination_message);
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
    public function getService_name(): ?string
    {
        return str_replace("\n", '<br>', $this->event->service->title ?? '');
    }

    /**
     * @return string
     */
    public function getService_url(): ?string
    {
        $user = User::find($this->event->service->user_id);
        return config('app.frontend_url') . str_replace(
                ['practitioner-slug', 'service-slug'],
                [$user->slug, $this->event->service->slug],
                config('app.frontend_public_service')
            );
    }


    /**
     * @return string
     */
    public function getAdd_to_calendar(): ?string
    {
        $this->calendarPresented = true;
        return $this->generateGoogleLink($this->event);
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
    public function getSchedule_name(): ?string
    {
        return $this->event->schedule->title;
    }

    /**
     * @return string
     */
    public function getRescheduled_Schedule_name(): ?string
    {
        return isset($this->event->reschedule) ? $this->event->reschedule->new_schedule->title : $this->event->schedule->title;
    }

    /**
     * @return string
     */
    public function getSchedule_start_date(): string
    {
        $startDate = $this->getEventStartDate($this->event);
        return $startDate !== null
            ? $this->convertToUserTimezone($startDate)->format(self::DATE_FORMAT)
            : '';
    }

    /**
     * @return string
     */
    public function getSchedule_start_time(): string
    {
        $startDate = $this->getEventStartDate($this->event);

        return $startDate !== null
            ? $this->convertToUserTimezone($startDate)->format(self::TIME_FORMAT)
            : '';
    }

    /**
     * @return string
     */
    public function getSchedule_end_date(): string
    {
        $endDate = $this->getEventEndDate($this->event);
        return $endDate !== null
            ? $this->convertToUserTimezone($endDate)->format(self::DATE_FORMAT)
            : '';
    }

    /**
     * @return string
     */
    public function getSchedule_end_time(): string
    {
        $endDate = $this->getEventEndDate($this->event);
        return $endDate !== null
            ? $this->convertToUserTimezone($endDate)->format(self::TIME_FORMAT)
            : '';
    }

    /**
     * @return string
     */
    public function getSchedule_venue_name(): ?string
    {
        return $this->event->schedule->venue_name;
    }

    /**
     * @return string
     */
    public function getSchedule_venue_address(): ?string
    {
        return $this->event->schedule->venue_address;
    }

    /**
     * @return string
     */
    public function getSchedule_city(): ?string
    {
        return $this->event->schedule->city;
    }

    /**
     * @return string
     */

    public function getSchedule_country(): ?string
    {
        return $this->event->schedule->country ? $this->event->schedule->country->nicename : '';
    }

    /**
     * @return string
     */
    public function getSchedule_postcode(): ?string
    {
        return $this->event->schedule->post_code;
    }

    /**
     * @return string
     */
    public function getSchedule_hosting_url(): ?string
    {
        return $this->event->schedule->url;
    }


    /**
     * @return string
     */
    public function getPractitioner_schedule_message(): ?string
    {
        return str_replace("\n", '<br>', $this->event->schedule->booking_message);
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
    public function getSubscription_tier_name(): string
    {
        return $this->event->plan->name;
    }

    /**
     * @return string
     */
    public function getSubscription_cost(): string
    {
        $price = isset($this->event->plan) ? number_format($this->event->plan->price, 2) : '';

        return config('app.platform_currency_sign')." ".$price;
    }

    /**
     * @return string
     */
    public function getSubscription_start_date(): string
    {
        return Carbon::parse($this->event->user->plan_from)->format(self::DATE_FORMAT);
    }

    /**
     * @return string
     */
    public function getSubscription_end_date(): string
    {
        return Carbon::parse($this->event->user->plan_until)->format(self::DATE_FORMAT);
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
    public function getMy_articles(): ?string
    {
        return config('app.frontend_url') . config('app.frontend_practitioner_articles');
    }

    /**
     * @return string
     */
    public function getArticle_url(): ?string
    {
        return isset($this->event->article) ? config('app.frontend_url') .
            str_replace(
                ['practitioner-slug', 'article-slug'],
                [$this->event->user->slug, $this->event->article->slug],
                config('app.frontend_practitioner_article_url')
            ) : '';
    }

    /**
     * @return string
     */
    public function getArticle_name(): ?string
    {
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
    public function getBooking_reference(): string
    {
        return $this->event->booking->reference;
    }

    /**
     * @return string
     */
    public function getView_client_booking(): string
    {
        $scheduleId = isset($this->event->reschedule) ? $this->event->reschedule->new_schedule_id : $this->event->booking->schedule->id;
        return config('app.frontend_url') . config('app.frontend_clients_booking_url') . $this->event->booking->purchase->service->id . '/' . $scheduleId;
    }

    /**
     * @return string
     */
    public function getView_client_purchase(): string
    {
        return config('app.frontend_url') . config('app.frontend_client_purchase_url') . $this->event->booking->purchase->service->id . '/' . $this->event->booking->id;
    }

    /**
     * @return string
     */
    public function getTotal_paid(): string
    {
        return config('app.platform_currency_sign')." ".$this->event->booking->cost;
    }

    /**
     * @return integer
     */
    public function getTickets_count(): int
    {
        return (int)$this->event->booking->amount;
    }

    /**
     * @return integer
     */
    public function getNumber_of_tickets_purchased(): int
    {
        return (int)$this->event->booking->amount;
    }

    /**
     * @return string
     */
    public function getClient_name(): string
    {
        return $this->event->client->first_name . ' ' . $this->event->client->last_name;
    }


    /**
     * @return string
     */
    public function getClient_email_address(): string
    {
        return $this->event->client->email ?? '';
    }

    /**
     * @return string
     */
    public function getSee_on_map(): string
    {
        $addressCollection = array_filter([
            $this->event->schedule->venue_name,
            $this->event->schedule->venue_address,
            $this->event->schedule->city,
            $this->event->schedule->country ? $this->event->schedule->country->nicename : ''
        ], static function (?string $value) {
            $trimmedValue = trim($value);
            if (!empty($trimmedValue)) {
                return str_replace(' ', '+', trim($value));
            }
        });
        return 'https://www.google.com/maps/search/?api=1&map_action=map&query=' .
            urlencode(implode(',', $addressCollection));
    }

    /**
     * {{accept}}
     *
     * @return string
     */
    public function getAccept(): string
    {
        return config('app.frontend_url') . config('app.frontend_reschedule_apply') . '/' . $this->event->reschedule->id . '?token=' .
            $this->generatePersonalAccessToken($this->event->client);
    }

    /**
     * {{decline}}
     */
    public function getDecline(): string
    {
        return config('app.frontend_url') . config('app.frontend_reschedule_amend_decline') . '/' . $this->event->reschedule->id . '?token=' .
            $this->generatePersonalAccessToken($this->event->client, 'decline');
    }

    /**
     * {{accept_amend}}
     */
    public function getAccept_amend(): string
    {
        return config('app.frontend_url') . config('app.frontend_accept_amend') . '/' . $this->event->reschedule->id . '?token=' .
            $this->generatePersonalAccessToken($this->event->client);
    }

    /**
     * {{decline_amend}}
     */
    public function getDecline_amend(): string
    {
        return config('app.frontend_url') . config('app.frontend_decline_amend') . '/' . $this->event->reschedule->id . '?token=' .
            $this->generatePersonalAccessToken($this->event->client, 'decline');
    }

    /**
     * @return string
     */
    public function getView_booking(): string
    {
        return config('app.frontend_url') . config('app.frontend_booking_url') . $this->event->booking->id;
    }

    /**
     * @return string
     */
    public function getView_my_booking(): string
    {
        return $this->getView_booking();
    }

    /**
     * @return string
     */
    public function getRebook(): string
    {
        return config('app.frontend_url') . config('app.frontend_booking_url') . $this->event->booking->id;
    }

    /**
     * @return string
     */
    public function getView_bookings(): string
    {
        return config('app.frontend_url') . config('app.frontend_booking_url');
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
    public function getReschedule_start_date(): string
    {
        return isset($this->event->reschedule)
        && $this->event->reschedule->new_start_date
            ? $this->convertToUserTimezone($this->event->reschedule->new_start_date)->format(self::DATE_FORMAT)
            : $this->convertToUserTimezone($this->event->schedule->start_date)->format(self::DATE_FORMAT);
    }

    /**
     * @return string
     */
    public function getReschedule_start_time(): string
    {
        return isset($this->event->reschedule)
        && $this->event->reschedule->new_start_date
            ? $this->convertToUserTimezone($this->event->reschedule->new_start_date)->format(self::TIME_FORMAT)
            : $this->convertToUserTimezone($this->event->schedule->start_date)->format(self::TIME_FORMAT);
    }

    /**
     * @return string
     */
    public function getReschedule_end_date(): string
    {
        return isset($this->event->reschedule)
        && $this->event->reschedule->new_end_date
            ? $this->convertToUserTimezone($this->event->reschedule->new_end_date)->format(self::DATE_FORMAT)
            : $this->convertToUserTimezone($this->event->schedule->end_date)->format(self::DATE_FORMAT);
    }

    /**
     * @return string
     */
    public function getReschedule_end_time(): string
    {
        return isset($this->event->reschedule)
        && $this->event->reschedule->new_end_date
            ? $this->convertToUserTimezone($this->event->reschedule->new_end_date)->format(self::TIME_FORMAT)
            : $this->convertToUserTimezone($this->event->schedule->end_date)->format(self::TIME_FORMAT);
    }

    /**
     * @return string
     */
    public function getReschedule_venue_name(): ?string
    {
        return isset($this->event->reschedule->new_schedule) ?
            $this->event->reschedule->new_schedule->venue_name : $this->event->schedule->venue_name;
    }

    /**
     * @return string
     */
    public function getReschedule_venue_address(): ?string
    {
        return isset($this->event->reschedule->new_schedule) ? $this->event->reschedule->new_schedule->venue_address : '';
    }

    /**
     * @return string
     */
    public function getReschedule_city(): ?string
    {
        return isset($this->event->reschedule->new_schedule) ?
            $this->event->reschedule->new_schedule->city : $this->event->schedule->city;
    }

    /**
     * @return string
     */
    public function getReschedule_country(): ?string
    {
        return isset($this->event->reschedule->new_schedule->country) ?
            $this->event->reschedule->new_schedule->country->nicename : ($this->event->schedule->country ? $this->event->schedule->country->nicename : '');
    }

    /**
     * @return string
     */
    public function getReschedule_postcode(): ?string
    {
        return isset($this->event->reschedule) ?
            $this->event->reschedule->new_schedule->post_code : $this->event->schedule->post_code;
    }

    /**
     * @return string
     */
    public function getReschedule_hosting_url(): ?string
    {
        return isset($this->event->reschedule) && isset($this->event->reschedule->new_url) ?
            $this->event->reschedule->new_url : $this->event->schedule->url;
    }

    /**
     * @return string
     */
    public function getPractitioner_reschedule_message(): ?string
    {
        $comment = $this->event instanceof
        BookingConfirmation ? $this->event->schedule->booking_message : $this->event->reschedule->new_schedule->comment;
        return str_replace("\n", '<br>', $comment);
    }

    /**
     * @return string
     */
    public function getClient_reschedule_message(): ?string
    {
        $comment = $this->event->reschedule->comment ?? '';
        return str_replace("\n", '<br>', $comment);
    }

    /**
     * @return string
     */
    public function getReschedule_comments(): ?string
    {
        $comment = $this->event->reschedule->comment ?? '';
        return str_replace("\n", '<br>', $comment);
    }

    /**
     * @return string
     */
    public function getPractitioner_message(): ?string
    {
        return $this->event instanceof BookingConfirmation ? $this->event->schedule->booking_message : '';
    }

    /**
     * @return string
     */
    public function getCheckout_comments(): ?string
    {
        $comment = $this->event->purchase->comment ?? '';
        return str_replace("\n", '<br>', $comment);
    }

    /**
     * @return string
     */
    public function getPrice_name(): ?string
    {
        return isset($this->event->price) && $this->event->price instanceof Price ? $this->event->price->name : '';
    }


    /**
     * RESCHEDULES END
     *
     */


    /**
     * @return string
     */
    public function getInstalments(): string
    {
        $str = '';
        if ($this->event->purchase) {
            $installments = Instalment::query()
                ->where('purchase_id', $this->event->purchase->id)
                ->where('is_paid', false)
                ->latest('created_at')
                ->orderBy('payment_date')
                ->get();
            foreach ($installments as $installment) {
                $str .= Carbon::parse($installment->payment_date)->format(self::DATE_FORMAT) . ' ' .
                    config('app.platform_currency_sign').
                    $installment->payment_amount . ' <br/>';
            }
        }
        return $str;
    }

    /**
     * @return string
     */
    public function getInstalments_next(): string
    {
        $str = '';
        if ($this->event->installmentNext) {
            $str = Carbon::parse($this->event->installmentNext->payment_date)->format(self::DATE_FORMAT) . ' ' .
                $this->event->installmentNext->payment_amount . ' <br/>';
        }
        return $str;
    }

    /**
     * @return string
     */
    public function getDeposit_paid(): string
    {
        return config('app.platform_currency_sign') . " " .
            (round($this->event->purchase->deposit_amount * $this->event->purchase->amount, 2, PHP_ROUND_HALF_DOWN) ?? 0);
    }

    /**
     * Changes timezone to user defined (+02:00, -06:45, etc)
     * All users default has London timezone
     *
     * @param $datetime
     * @return Carbon
     */
    private function convertToUserTimezone($datetime): Carbon
    {
        $timezone = isset($this->event->recipient) ? $this->event->recipient->user_timezone->value : $this->event->user->user_timezone->value;
        return Carbon::parse($datetime)->setTimezone($timezone);
    }
}
