<?php

use App\Models\CustomEmail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
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
                'name'       => 'Booking Confirmation - DateLess Physical With Deposit',
                'user_type'  => 'client',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject'    => 'Purchase Confirmation - {{service_name}}',
                'logo'       => "",
                'text'       => '<tr><td>Hi {{first_name}} <br/>
            Congratulations! {{client_name}} has purchased {{service_name}}. <br/><br/>
            Purchase Details: {{service_name}} - {{schedule_name}}<br/>
            Booking Reference: {{booking_reference}} <br/>
            Location: {{schedule_city}}, {{schedule_country}} {{view_booking}} <br/>
            The Client has paid a deposit of {{deposit_paid]} and will pay the remaining over instalments as follows:<br/>
            {{instalments}}<br/><br/>
            Thank you <br/>The {{platform_name}} Team<br/></td></tr>',
                'delay'      => random_int(5, 20)
            ],
            [
                'name'       => 'Booking Confirmation - DateLess Physical With Deposit',
                'user_type'  => 'practitioner',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject'    => 'Purchase Confirmation - {{booking_reference}} - {{service_name}}',
                'logo'       => "",
                'text'       => '<tr><td>Hi {{first_name}}<br/>
             Your purchase for {{service_name}} is now confirmed with {{practitioner_business_name}}<br/><br/>
             Purchase Details: {{service_name}} - {{schedule_name}} <br/>
             Booking Reference: {{booking_reference}}<br/>
             Location: {{schedule_city}}, {{schedule_country}} <br/>{{view_booking}} <br/>
             Message from {{practitioner_business_name}}<br/>
             {{practitioner_booking_message}} <br/>
             Your Practitioner may have also added some attachments to this email for you.<br/>
             Payment Deposit Paid: {{deposit_paid}} <br/>
             The balance for this service will be charged to your card provided as follows:<br/>
             {{instalments}}<br/>
             Please make sure you have funds available for each instalment or your purchase may be cancelled.<br/><br/>
             Thank you <br/>The {{platform_name}} Team<br/></td></tr>',
                'delay'      => random_int(5, 20)
            ],
            [
                'name'       => 'Booking Confirmation - Date Physical With Deposit',
                'user_type'  => 'client',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject'    => 'Booking Confirmation - {{booking_reference}} - {{service_name}}',
                'logo'       => "",
                'text'       => '<tr><td>Hi {{first_name}}<br/> Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}<br/>
            Booking Details: {{service_name}} - {{schedule_name}}<br/>
            Booking Reference: {{booking_reference}} <br/>
            From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}<br/>
            Location:  {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}}
            <br/><a href="{{add_to_calendar}}" target="_blank">Add to calendar</a>{{see_on_map}}<br/>
            Message from {{practitioner_business_name}}<br/>
            {{practitioner_booking_message}} <br/>
            Your Practitioner may have also added some attachments to this email for you. <br/>Payment Deposit Paid: {{deposit_paid]} <br/>
            The balance for this service will be charged to your card provided as follows:<br/>
            {{instalments}}<br/>
            Please make sure you have funds available for each instalment or your Booking may be cancelled. <br/><br/>
            Thank you <br/>The {{platform_name}} Team<br/></td></tr>',
                'delay'      => random_int(5, 20)
            ],
            [
                'name'       => 'Booking Confirmation - Date Physical With Deposit',
                'user_type'  => 'practitioner',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject'    => 'Booking Confirmation - {{service_name}}',
                'logo'       => "",
                'text'       => '<tr><td>Hi {{first_name}} <br/>
            Congratulations! {{client_name}}  has booked with you for {{service_name}}. <br/>
            Booking Details: {{service_name}} - {{schedule_name}}<br/>
            Booking Reference: {{booking_reference}} <br/>
            From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}} <br/>
            Location: {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}}<br/>
            {{view_booking}} <br/>
            The Client has paid a deposit of {{deposit_paid}} and will pay the remaining over instalments as follows:<br/>
            {{instalments}}<br/><br/>
            Thank you <br/>The {{platform_name}} Team<br/></td></tr>',
                'delay'      => random_int(5, 20)
            ],
            [
                'name'       => 'Booking Confirmation - DateLess Virtual With Deposit',
                'user_type'  => 'practitioner',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject'    => 'Purchase Confirmation - {{service_name}}',
                'logo'       => "",
                'text'       => '<tr><td>Hi {{first_name}} <br/>
            Congratulations! {{client_name}} has purchased {{service_name}}.<br/>
            Purchase Details: {{service_name}} - {{schedule_name}}<br/>
            Order Reference: {{booking_reference}} <br/>
            Location: {{schedule_hosting_url}} <br/>{{view_booking}}<br/>
            The Client has paid a deposit of {{deposit_paid]} and will pay the remaining over instalments as follows:<br/>
            {{instalments}}<br/><br/> Thank you<br/> The {{platform_name}}  Team<br/></td></tr>',
                'delay'      => random_int(5, 20)
            ],
            [
                'name'       => 'Booking Confirmation - DateLess Virtual With Deposit',
                'user_type'  => 'client',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject'    => 'Order Confirmation - {{booking_reference}} - {{service_name}}',
                'logo'       => "",
                'text'       => '<tr><td>Hi {{first_name}} <br/>
            Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}  <br/>
            Purchase Details: {{service_name}} - {{schedule_name}}<br/>
            Order Reference: {{booking_reference}} <br/>
            Location: {{schedule_hosting_url}} <br/>{{view_booking}} <br/>
            Message from {{practitioner_business_name}}
            <br/>{{practitioner_booking_message}} <br/>
            Your Practitioner may have also added some attachments to this email for you and should also be in touch with you via {{platform_name}} email message to confirm further details.
            <br/>Payment Deposit Paid: {{deposit_paid}} <br/>
            The balance for this service will be charged to your card proved as follows:<br/>
            {{instalments}} <br/>Please make sure you have funds available for each instalment or your purchase may be cancelled. <br/><br/>
            Thank you <br/>The {{platform_name}}  Team<br/></td></tr>',
                'delay'      => random_int(5, 20)
            ],
            [
                'name'       => 'Booking Confirmation - Event Virtual With Deposit',
                'user_type'  => 'practitioner',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject'    => 'Booking Confirmation - {{service_name}}',
                'logo'       => "",
                'text'       => '<tr><td>Hi {{first_name}} <br/>Congratulations! {{client_name}} has booked with you for {{service_name}}. <br/>
            Booking Details: {{service_name}} - {{schedule_name}}<br/>
            Booking Reference: {{booking_reference}} <br/>
            From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}<br/>
            Location: {{schedule_hosting_url}}<br/> {{view_booking}}<br/>
            The Client has paid a deposit of {{deposit_paid]} and will pay the remaining over instalments as follows:<br/>
            {{instalments}}<br/><br/> Thank you <br/>The {{platform_name}}  Team<br/></td></tr>',
                'delay'      => random_int(5, 20)
            ],
            [
                'name'       => 'Booking Confirmation - Event Virtual With Deposit',
                'user_type'  => 'client',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject'    => 'Booking Confirmation - {{booking_reference}} - {{service_name}}',
                'logo'       => "",
                'text'       => '<tr><td>Hi {{first_name}}<br/>
            Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}. <br/>
            Booking Details: {{service_name}} - {{schedule_name}} <br/>
            Booking Reference: {{booking_reference}} <br/>
            From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}} <br/>
            Location: {{schedule_hosting_url}} <br/>
            <a href="{{add_to_calendar}}" target="_blank">Add to calendar</a> <br/>
             <a href="{{view_booking}}" target="_blank">View My Bookings</a> <br/>
            Message from {{practitioner_business_name}} <br/>{{practitioner_booking_message}} <br/>
            Your Practitioner may have also added some attachments to this email for you.
            Payment Deposit Paid: {{deposit_paid}} The balance for this service will be charged to your card proved as follows:<br/>
            {{instalments}} <br/>
            Please make sure you have funds available for each instalment or your Booking may be cancelled. <br/>
            <br/>Thank you<br/> The {{platform_name}}  Team<br/></td></tr>',
                'delay'      => random_int(5, 20)
            ],
            [
                'name'       => 'Instalment Payment Reminder',
                'user_type'  => 'client',
                'from_email' => config('app.platform_email'),
                'from_title' => config('app.platform_name'),
                'subject'    => 'Payment Reminder {{booking_reference}} - {{service_name}}',
                'logo'       => "",
                'text'       => '<tr><td>Hi {{first_name}} <br/>
            This is to remind you that your next instalment payment for {{service_name}} from {{practitioner_business_name}} is due in 7 days.
            The Instalment Payment Schedule is charged to your card provided as follows:
            <br/>{{instalments_next}}<br />
            Service: {{service_name}} - {{schedule_name}} <br/>
            Booking Reference: {{booking_reference}} <br/>
            <a href="{{view_booking}}" target="_blank">View My Booking</a><br/><br/>
            Thank you<br/>
            The {{platform_name}} Team<br/></td></tr>',
                'delay'      => random_int(5, 20)
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
