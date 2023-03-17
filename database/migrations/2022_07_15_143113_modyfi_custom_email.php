<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\CustomEmail;

class ModyfiCustomEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        CustomEmail::upsert([
            ['id' => 109, 'text' => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Your Client, {{client_name}} has rescheduled their booking for {{service_name}}.</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">They are now booked in for: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">From: {{reschedule_start_date}}, {{reschedule_start_time}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">To: {{reschedule_end_date}}, {{reschedule_end_time}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Location: {{reschedule_venue_name}} {{reschedule_venue_address}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Message from {{client_name}}:</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">{{client_reschedule_message}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Their original booking will be reopened in your service schedule for resale.</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Original Booking: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}} {{schedule_venue_name}} </p></td></tr><tr><td><p class="slate-p">{{schedule_venue_address}}{{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>'],
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
        CustomEmail::upsert([
            ['id' => 109, 'text' => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Your Client, {{client_name}} has rescheduled their booking for {{service_name}}.</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">They are now booked in for: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">From: {{reschedule_start_date}}, {{reschedule_start_time}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">To: {{reschedule_end_date}}, {{reschedule_end_time}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Location: {{reschedule_venue_name}} {{reschedule_venue_address}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Message from {{client_name}}:</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">{{client_reschedule_message}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Their original booking will be reopened in your service schedule for resale.</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Original Booking: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}} {{schedule_venue_name}} </p></td></tr><tr><td><p class="slate-p">{{schedule_venue_address}}{{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>'],
        ],
            ['id'],
            ['text']
        );
    }
}
