<?php

use App\Models\CustomEmail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixTemplatesLayout extends Migration
{
    public function up()
    {

        $customEmailsNew = [
            [
                'name' => 'Booking Confirmation - DateLess Physical With Deposit',
                'text' => '<tr> <td><p class="slate-p">Hi {{first_name}} </p></td> </tr>
                    <tr> <td> <p class="slate-p"></p> </td> </tr>
                    <tr> <td> <p class="slate-p">Congratulations! {{client_name}} has purchased {{service_name}}.</p> </td> </tr>
                    <tr> <td> <p class="slate-p">Booking Reference: {{booking_reference}} </p> </td> </tr>
                    <tr> <td> <p class="slate-p">Location: {{schedule_city}}, {{schedule_country}} {{view_booking}}</p> </td> </tr>
                    <tr> <td> <p class="slate-p">The Client has paid a deposit of {{deposit_paid]} and will pay the remaining over instalments as follows:</p> </td> </tr>
                    <tr> <td> <p class="slate-p">{{instalments}}</p> </td> </tr>
                    <tr> <td> <p class="slate-p">Thank you The {{platform_name}} Team</p> </td> </tr>',
            ],
            [
                'name' => 'Booking Confirmation - DateLess Physical With Deposit',
                'text' => '
                    <tr> <td> <p class="slate-p">Hi {{first_name}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Your purchase for {{service_name}} is now confirmed with {{practitioner_business_name}} </p> </td></tr>
                    <tr> <td> <p class="slate-p">Purchase Details: {{service_name}} - {{schedule_name}}  </p> </td></tr>
                    <tr> <td> <p class="slate-p">Booking Reference: {{booking_reference}}  </p></td></tr>
                    <tr> <td> <p class="slate-p">Location: {{schedule_city}}, {{schedule_country}} {{view_booking}}   </p></td></tr>
                    <tr> <td> <p class="slate-p">Message from {{practitioner_business_name}} </p> </td></tr>
                    <tr> <td> <p class="slate-p">{{practitioner_booking_message}}   </p></td></tr>
                    <tr> <td> <p class="slate-p">Your Practitioner may have also added some attachments to this email for you. </p> </td></tr>
                    <tr> <td> <p class="slate-p">Payment Deposit Paid: {{deposit_paid}} </p>  </td></tr>
                    <tr> <td> <p class="slate-p">The balance for this service will be charged to your card provided as follows:  </p></td></tr>
                    <tr> <td> <p class="slate-p">{{instalments}}  </p></td></tr>
                    <tr> <td> <p class="slate-p">Please make sure you have funds available for each instalment or your purchase may be cancelled. </p> </td></tr>
                    <tr> <td> <p class="slate-p">Thank you The {{platform_name}} Team  </p></td></tr>',
            ],
            [
                'name' => 'Booking Confirmation - Date Physical With Deposit',
                'text' => '
                    <tr> <td> <p class="slate-p">Hi {{first_name}} Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Reference: {{booking_reference}} </p></td></tr>
                    <tr> <td> <p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Location:  {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}}</p></td></tr>
                    <tr> <td> <p class="slate-p"><a href="{{add_to_calendar}}" target="_blank">Add to calendar</a>{{see_on_map}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Message from {{practitioner_business_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">{{practitioner_booking_message}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Your Practitioner may have also added some attachments to this email for you. Payment Deposit Paid: {{deposit_paid]} </p></td></tr>
                    <tr> <td> <p class="slate-p">The balance for this service will be charged to your card provided as follows:</p></td></tr>
                    <tr> <td> <p class="slate-p">{{instalments}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Please make sure you have funds available for each instalment or your Booking may be cancelled. </p></td></tr>
                    <tr> <td> <p class="slate-p">Thank you The {{platform_name}} Team</p></td></tr>',
            ],
            [
                'name' => 'Booking Confirmation - Date Physical With Deposit',
                'text' => '
                    <tr> <td> <p class="slate-p">Hi {{first_name}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Congratulations! {{client_name}}  has booked with you for {{service_name}}. </p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Reference: {{booking_reference}} </p></td></tr>
                    <tr> <td> <p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Location: {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}}</p></td></tr>
                    <tr> <td> <p class="slate-p">{{view_booking}} </p></td></tr>
                    <tr> <td> <p class="slate-p">The Client has paid a deposit of {{deposit_paid}} and will pay the remaining over instalments as follows:</p></td></tr>
                    <tr> <td> <p class="slate-p">{{instalments}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Thank you The {{platform_name}} Team</p></td></tr>',
            ],
            [
                'name' => 'Booking Confirmation - DateLess Virtual With Deposit',
                'text' => '
                    <tr> <td> <p class="slate-p">Hi {{first_name}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Congratulations! {{client_name}} has purchased {{service_name}}.</p></td></tr>
                    <tr> <td> <p class="slate-p">Purchase Details: {{service_name}} - {{schedule_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Order Reference: {{booking_reference}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Location: {{schedule_hosting_url}} {{view_booking}}</p></td></tr>
                    <tr> <td> <p class="slate-p">The Client has paid a deposit of {{deposit_paid]} and will pay the remaining over instalments as follows:</p></td></tr>
                    <tr> <td> <p class="slate-p">{{instalments}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Thank you The {{platform_name}}  Team</p></td></tr>',
            ],
            [
                'name' => 'Booking Confirmation - DateLess Virtual With Deposit',
                'text' => '
                    <tr> <td> <p class="slate-p">Hi {{first_name}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}  </p></td></tr>
                    <tr> <td> <p class="slate-p">Purchase Details: {{service_name}} - {{schedule_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Order Reference: {{booking_reference}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Location: {{schedule_hosting_url}} {{view_booking}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Message from {{practitioner_business_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">{{practitioner_booking_message}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Your Practitioner may have also added some attachments to this email for you and should also be in touch with you via {{platform_name}} email message to confirm further details.</p></td></tr>
                    <tr> <td> <p class="slate-p">&nbsp;</p></td></tr>
                    <tr> <td> <p class="slate-p">Payment Deposit Paid: {{deposit_paid}} </p></td></tr>
                    <tr> <td> <p class="slate-p">The balance for this service will be charged to your card proved as follows:</p></td></tr>
                    <tr> <td> <p class="slate-p">{{instalments}} Please make sure you have funds available for each instalment or your purchase may be cancelled. </p></td></tr>
                    <tr> <td> <p class="slate-p">Thank you The {{platform_name}}  Team</p></td></tr>',
            ],
            [
                'name' => 'Booking Confirmation - Event Virtual With Deposit',
                'text' => '
                    <tr> <td> <p class="slate-p">Hi {{first_name}} Congratulations! {{client_name}} has booked with you for {{service_name}}. </p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Reference: {{booking_reference}} </p></td></tr>
                    <tr> <td> <p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Location: {{schedule_hosting_url}} {{view_booking}}</p></td></tr>
                    <tr> <td> <p class="slate-p">The Client has paid a deposit of {{deposit_paid]} and will pay the remaining over instalments as follows:</p></td></tr>
                    <tr> <td> <p class="slate-p">{{instalments}} Thank you The {{platform_name}}  Team</p></td></tr>',
            ],
            [
                'name' => 'Booking Confirmation - Event Virtual With Deposit',
                'text' => '
                    <tr> <td> <p class="slate-p">Hi {{first_name}}</p></td></tr>
                    <tr> <td> <p class="slate-p">Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}. </p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Booking Reference: {{booking_reference}} </p></td></tr>
                    <tr> <td> <p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Location: {{schedule_hosting_url}} </p></td></tr>
                    <tr> <td> <p class="slate-p"><a href="{{add_to_calendar}}" target="_blank">Add to calendar</a> </p></td></tr>
                    <tr> <td> <p class="slate-p"><a href="{{view_booking}}" target="_blank">View My Bookings</a> </p></td></tr>
                    <tr> <td> <p class="slate-p">Message from {{practitioner_business_name}} {{practitioner_booking_message}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Your Practitioner may have also added some attachments to this email for you.</p></td></tr>
                    <tr> <td> <p class="slate-p">Payment Deposit Paid: {{deposit_paid}} The balance for this service will be charged to your card proved as follows:</p></td></tr>
                    <tr> <td> <p class="slate-p">{{instalments}} </p></td></tr>
                    <tr> <td> <p class="slate-p">Please make sure you have funds available for each instalment or your Booking may be cancelled. </p></td></tr>
                    <tr> <td> <p class="slate-p">Thank you The {{platform_name}}  Team</p></td></tr>',
            ],
            [
                'name' => 'Instalment Payment Reminder',
                'text' => '
                <tr> <td> <p class="slate-p">Hi {{first_name}} </p></td></tr>
                <tr> <td> <p class="slate-p">This is to remind you that your next instalment payment for {{service_name}} from {{practitioner_business_name}} is due in 7 days. </p></td></tr>
                <tr> <td> <p class="slate-p">The Instalment Payment Schedule is charged to your card provided as follows: </p></td></tr>
                <tr> <td> <p class="slate-p">{{instalments_next}} </p></td></tr>
                <tr> <td> <p class="slate-p">Service: {{service_name}} - {{schedule_name}} </p></td></tr>
                <tr> <td> <p class="slate-p">Booking Reference: {{booking_reference}} </p></td></tr>
                <tr> <td> <p class="slate-p"><a href="{{view_booking}}" target="_blank">View My Booking</a></p></td></tr>
                <tr> <td> <p class="slate-p">Thank you</p></td></tr>
                <tr> <td> <p class="slate-p">The {{platform_name}} Team</p></td></tr>',
            ],
        ];
        foreach ($customEmailsNew as $email) {
            $mail = CustomEmail::where('name',$email['name'])->first();
            $mail->text = $email['text'];
            $mail->save();
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
