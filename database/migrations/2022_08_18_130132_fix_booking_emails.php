<?php

use App\Models\CustomEmail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixBookingEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        CustomEmail::upsert(
            [
                ['id' => 89, 'text' => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}}{{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}} {{schedule_postcode}} {{schedule_country}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"><a href="{{add_to_calendar}}" class="slate-link">Add to calendar</a></p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"><a href="{{view_booking}}" class="slate-link">View My Bookings</a></p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Your Practitioner may have also added some attachments to your booking.</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"><strong class="slate-bold">Booking Details: </strong></p></td></tr><tr><td><p class="slate-p">{{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}}{{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}} {{schedule_postcode}} {{schedule_country}}</p></td></tr>'],
                ['id' => 90, 'text' => '<tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Congratulations! {{client_name}} has booked with you for {{service_name}}.</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"><strong class="slate-bold">Booking Details:</strong></p></td></tr><tr><td><p class="slate-p">{{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">Start date: {{schedule_start_date}} at {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">End date: {{schedule_end_date}} at {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}}{{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}} {{schedule_postcode}} {{schedule_country}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><a href="{{view_client_purchase}}" target="_blank" rel="noreferrer" data-button-link="true" style="text-decoration:none"><span style="padding:10px 24px;margin-bottom:8px;color:#ffffff;text-align:center;background-color:#db7a6a;line-height:45px;border-radius:25px;cursor:pointer;user-select:none" contenteditable="false" >View Client Booking</span></a></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"><strong class="slate-bold">Purchase Details:</strong></p></td></tr><tr><td><p class="slate-p">Price option: <span data-slate-template="true">{{price_name}}</span> </p></td></tr><tr><td><p class="slate-p">Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p">Quantity: <span data-slate-template="true">{{number_of_tickets_purchased}}</span> </p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The Holistify Team</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"></p></td></tr>'],
            ],
            ['id'],
            ['text']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        CustomEmail::upsert(
            [
                ['id' => 89, 'text' => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}}{{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}} {{schedule_postcode}} {{schedule_country}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"><a href="{{add_to_calendar}}" class="slate-link">Add to calendar</a></p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"><a href="{{view_booking}}" class="slate-link">View My Bookings</a></p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Your Practitioner may have also added some attachments to your booking.</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"><strong class="slate-bold">Booking Details: </strong></p></td></tr><tr><td><p class="slate-p">{{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}}{{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}} {{schedule_postcode}} {{schedule_country}}</p></td></tr>'],
                ['id' => 90, 'text' => '<tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Congratulations! {{client_name}} has booked with you for {{service_name}}.</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"><strong class="slate-bold">Booking Details:</strong></p></td></tr><tr><td><p class="slate-p">{{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">Start date: {{schedule_start_date}} at {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">End date: {{schedule_end_date}} at {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}}{{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}} {{schedule_postcode}} {{schedule_country}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><a href="{{view_client_purchase}}" target="_blank" rel="noreferrer" data-button-link="true" style="text-decoration:none"><span style="padding:10px 24px;margin-bottom:8px;color:#ffffff;text-align:center;background-color:#db7a6a;line-height:45px;border-radius:25px;cursor:pointer;user-select:none" contenteditable="false" >View Client Booking</span></a></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"><strong class="slate-bold">Purchase Details:</strong></p></td></tr><tr><td><p class="slate-p">Price option: <span data-slate-template="true">{{price_name}}</span> </p></td></tr><tr><td><p class="slate-p">Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p">Quantity: <span data-slate-template="true">{{number_of_tickets_purchased}}</span> </p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The Holistify Team</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"></p></td></tr>'],
            ],
            ['id'],
            ['text']
        );
    }
}
