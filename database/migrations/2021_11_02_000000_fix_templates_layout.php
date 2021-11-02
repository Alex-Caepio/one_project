<?php

use App\Models\CustomEmail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixTemplatesLayout extends Migration
{
    public function up()
    {
        CustomEmail::whereIn(
            'name',
            [
                'Instalment Payment Reminder',
                "Booking Event Virtual - with Deposit",
                "Booking Confirmation - Event Virtual With Deposit",
                "Booking Confirmation - DateLess Virtual with Deposit",
                "Booking Confirmation Dateless Virtual – with Deposit",
                "Booking Confirmation - Date Physical - with Deposit",
                "Booking Confirmation - DateLess Physical - with Deposit"
            ]
        )->delete();

        $customEmailsNew = [
            [
                'name' => 'Booking Confirmation - DateLess Physical With Deposit',
                'user_type' => 'client',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject' => 'Purchase Confirmation - {{service_name}}',
                'logo' => "",
                'text' => '<tr> <td><p class="slate-p">Hi {{first_name}} </p></td> </tr>
                    <tr> <td> <p class="slate-p"></p> </td> </tr>
                    <tr> <td> <p class="slate-p">Congratulations! {{client_name}} has purchased {{service_name}}.</p> </td> </tr>
                    <tr> <td> <p class="slate-p">Booking Reference: {{booking_reference}} </p> </td> </tr>
                    <tr> <td> <p class="slate-p">Location: {{schedule_city}}, {{schedule_country}} {{view_booking}}</p> </td> </tr>
                    <tr> <td> <p class="slate-p">The Client has paid a deposit of {{deposit_paid]} and will pay the remaining over instalments as follows:</p> </td> </tr>
                    <tr> <td> <p class="slate-p">{{instalments}}</p> </td> </tr>
                    <tr> <td> <p class="slate-p">Thank you <br/>The {{platform_name}} Team</p> </td> </tr>',
                'delay' => random_int(5, 20)
            ],
            [
                'name' => 'Booking Confirmation - DateLess Physical With Deposit',
                'user_type' => 'practitioner',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject' => 'Purchase Confirmation - {{booking_reference}} - {{service_name}}',
                'logo' => "",
                'text' => '
                    <tr> <td> <p class="slate-p">Hi {{first_name}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Your purchase for {{service_name}} is now confirmed with {{practitioner_business_name}} </p> </td></tr>
                    <tr> <td> <p class="slate-p">Purchase Details: {{service_name}} - {{schedule_name}}  </p> </td></tr>
                    <tr> <td> <p class="slate-p">Booking Reference: {{booking_reference}}  </p></td></tr>
                    <tr> <td> <p class="slate-p">Location: {{schedule_city}}, {{schedule_country}} <br/>{{view_booking}}   </p></td></tr>
                    <tr> <td> <p class="slate-p">Message from {{practitioner_business_name}} </p> </td></tr>
                    <tr> <td> <p class="slate-p">{{practitioner_booking_message}}   </p></td></tr>
                    <tr> <td> <p class="slate-p">Your Practitioner may have also added some attachments to this email for you. </p> </td></tr>
                    <tr> <td> <p class="slate-p">Payment Deposit Paid: {{deposit_paid}} </p>  </td></tr>
                    <tr> <td> <p class="slate-p">The balance for this service will be charged to your card provided as follows:  </p></td></tr>
                    <tr> <td> <p class="slate-p">{{instalments}}  </p></td></tr>
                    <tr> <td> <p class="slate-p">Please make sure you have funds available for each instalment or your purchase may be cancelled. </p> </td></tr>
                    <tr> <td> <p class="slate-p">Thank you <br/>The {{platform_name}} Team  </p></td></tr>',
                'delay' => random_int(5, 20)
            ],
            [
                'name' => 'Booking Confirmation - Date Physical With Deposit',
                'user_type' => 'client',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject' => 'Booking Confirmation - {{booking_reference}} - {{service_name}}',
                'logo' => "",
                'text' => '
                    <tr> <td> <p class="slate-p">Hi {{first_name}}<br/> Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Reference: {{booking_reference}} </p></td></tr>
                    <tr> <td> <p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Location:  {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}}
                    <tr> <td> <p class="slate-p"><a href="{{add_to_calendar}}" target="_blank">Add to calendar</a>{{see_on_map}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Message from {{practitioner_business_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">{{practitioner_booking_message}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Your Practitioner may have also added some attachments to this email for you. <br/>Payment Deposit Paid: {{deposit_paid]} </p></td></tr>
                    <tr> <td> <p class="slate-p">The balance for this service will be charged to your card provided as follows:</p></td></tr>
                    <tr> <td> <p class="slate-p">{{instalments}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Please make sure you have funds available for each instalment or your Booking may be cancelled. <br/></p></td></tr>
                    <tr> <td> <p class="slate-p">Thank you <br/>The {{platform_name}} Team</p></td></tr>',
                'delay' => random_int(5, 20)
            ],
            [
                'name' => 'Booking Confirmation - Date Physical With Deposit',
                'user_type' => 'practitioner',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject' => 'Booking Confirmation - {{service_name}}',
                'logo' => "",
                'text' => '
                    <tr> <td> <p class="slate-p">Hi {{first_name}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Congratulations! {{client_name}}  has booked with you for {{service_name}}. </p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Reference: {{booking_reference}} </p></td></tr>
                    <tr> <td> <p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Location: {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}}</p></td></tr>
                    <tr> <td> <p class="slate-p">{{view_booking}} </p></td></tr>
                    <tr> <td> <p class="slate-p">The Client has paid a deposit of {{deposit_paid}} and will pay the remaining over instalments as follows:</p></td></tr>
                    <tr> <td> <p class="slate-p">{{instalments}}<br/></p></td></tr>
                    <tr> <td> <p class="slate-p">Thank you <br/>The {{platform_name}} Team</p></td></tr>',
                'delay' => random_int(5, 20)
            ],
            [
                'name' => 'Booking Confirmation - DateLess Virtual With Deposit',
                'user_type' => 'practitioner',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject' => 'Purchase Confirmation - {{service_name}}',
                'logo' => "",
                'text' => '
                    <tr> <td> <p class="slate-p">Hi {{first_name}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Congratulations! {{client_name}} has purchased {{service_name}}.</p></td></tr>
                    <tr> <td> <p class="slate-p">Purchase Details: {{service_name}} - {{schedule_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Order Reference: {{booking_reference}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Location: {{schedule_hosting_url}} <br/>{{view_booking}}</p></td></tr>
                    <tr> <td> <p class="slate-p">The Client has paid a deposit of {{deposit_paid]} and will pay the remaining over instalments as follows:</p></td></tr>
                    <tr> <td> <p class="slate-p">{{instalments}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Thank you<br/> The {{platform_name}}  Team</p></td></tr>',
                'delay' => random_int(5, 20)
            ],
            [
                'name' => 'Booking Confirmation - DateLess Virtual With Deposit',
                'user_type' => 'client',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject' => 'Order Confirmation - {{booking_reference}} - {{service_name}}',
                'logo' => "",
                'text' => '
                    <tr> <td> <p class="slate-p">Hi {{first_name}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}  </p></td></tr>
                    <tr> <td> <p class="slate-p">Purchase Details: {{service_name}} - {{schedule_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Order Reference: {{booking_reference}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Location: {{schedule_hosting_url}} <br/>{{view_booking}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Message from {{practitioner_business_name}}
                    <tr> <td> <p class="slate-p">{{practitioner_booking_message}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Your Practitioner may have also added some attachments to this email for you and should also be in touch with you via {{platform_name}} email message to confirm further details.</p></td></tr>
                    <tr> <td> <p class="slate-p"></p></td></tr>
                    <tr> <td> <p class="slate-p">Payment Deposit Paid: {{deposit_paid}} </p></td></tr>
                    <tr> <td> <p class="slate-p">The balance for this service will be charged to your card proved as follows:</p></td></tr>
                    <tr> <td> <p class="slate-p">{{instalments}} <br/>Please make sure you have funds available for each instalment or your purchase may be cancelled. </p></td></tr>
                    <tr> <td> <p class="slate-p">Thank you <br/>The {{platform_name}}  Team</p></td></tr>',
                'delay' => random_int(5, 20)
            ],
            [
                'name' => 'Booking Confirmation - Event Virtual With Deposit',
                'user_type' => 'practitioner',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject' => 'Booking Confirmation - {{service_name}}',
                'logo' => "",
                'text' => '
                    <tr> <td> <p class="slate-p">Hi {{first_name}} <br/>Congratulations! {{client_name}} has booked with you for {{service_name}}. </p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Reference: {{booking_reference}} </p></td></tr>
                    <tr> <td> <p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Location: {{schedule_hosting_url}}<br/> {{view_booking}}</p></td></tr>
                    <tr> <td> <p class="slate-p">The Client has paid a deposit of {{deposit_paid]} and will pay the remaining over instalments as follows:</p></td></tr>
                    <tr> <td> <p class="slate-p">{{instalments}}<br/><br/> Thank you <br/>The {{platform_name}}  Team</p></td></tr>',
                'delay' => random_int(5, 20)
            ],
            [
                'name' => 'Booking Confirmation - Event Virtual With Deposit',
                'user_type' => 'client',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject' => 'Booking Confirmation - {{booking_reference}} - {{service_name}}',
                'logo' => "",
                'text' => '
                    <tr> <td> <p class="slate-p">Hi {{first_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}. </p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Reference: {{booking_reference}} </p></td></tr>
                    <tr> <td> <p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Location: {{schedule_hosting_url}} </p></td></tr>
                    <tr> <td> <p class="slate-p"><a href="{{add_to_calendar}}" target="_blank">Add to calendar</a> </p></td></tr>
                    <tr> <td> <p class="slate-p"><a href="{{view_booking}}" target="_blank">View My Bookings</a> </p></td></tr>
                    <tr> <td> <p class="slate-p">Message from {{practitioner_business_name}} <br/>{{practitioner_booking_message}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Your Practitioner may have also added some attachments to this email for you.
                    <tr> <td> <p class="slate-p">Payment Deposit Paid: {{deposit_paid}} The balance for this service will be charged to your card proved as follows:</p></td></tr>
                    <tr> <td> <p class="slate-p">{{instalments}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Please make sure you have funds available for each instalment or your Booking may be cancelled. </p></td></tr>
                    <tr> <td> <p class="slate-p">Thank you<br/> The {{platform_name}}  Team</p></td></tr>',
                'delay' => random_int(5, 20)
            ],
            [
                'name' => 'Instalment Payment Reminder',
                'user_type' => 'client',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject' => 'Payment Reminder {{booking_reference}} - {{service_name}}',
                'logo' => "",
                'text' => '
                <tr> <td> <p class="slate-p">Hi {{first_name}} </p></td></tr>
                <tr> <td> <p class="slate-p">This is to remind you that your next instalment payment for {{service_name}} from {{practitioner_business_name}} is due in 7 days. </p></td></tr>
                <tr> <td> <p class="slate-p">The Instalment Payment Schedule is charged to your card provided as follows: </p></td></tr>
                <tr> <td> <p class="slate-p"><br/>{{instalments_next}} </p></td></tr>
                <tr> <td> <p class="slate-p">Service: {{service_name}} - {{schedule_name}} </p></td></tr>
                <tr> <td> <p class="slate-p">Booking Reference: {{booking_reference}} </p></td></tr>
                <tr> <td> <p class="slate-p"><a href="{{view_booking}}" target="_blank">View My Booking</a><br/></p></td></tr>
                <tr> <td> <p class="slate-p">Thank you</p></td></tr>
                <tr> <td> <p class="slate-p">The {{platform_name}} Team</p></td></tr>',
                'delay' => random_int(5, 20)
            ],
        ];
        foreach ($customEmailsNew as $email) {
            CustomEmail::create($email);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
