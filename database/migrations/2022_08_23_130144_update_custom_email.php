<?php

use App\Models\CustomEmail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCustomEmail extends Migration
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
                ['id' => 93, 'text' => '<tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Location: {{schedule_venue_name}} {{schedule_venue_address}} </p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >{{schedule_city}}, {{schedule_postcode}} {{schedule_country}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >{{see_on_map}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Message from {{practitioner_business_name}}:</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >{{practitioner_schedule_message}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Your Practitioner may have also added some attachments to this email for you.</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" ><strong class="slate-bold"  >Booking Details: </strong></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >{{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Location: {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Thank you</p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >The <span data-slate-template="true">{{platform_name}}</span> Team </p></td></tr>']
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
                ['id' => 93, 'text' => '<tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Location: {{schedule_venue_name}} {{schedule_venue_address}} </p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >{{schedule_city}}, {{schedule_postcode}} {{schedule_country}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >{{see_on_map}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Message from {{practitioner_business_name}}:</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >{{practitioner_schedule_message}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Your Practitioner may have also added some attachments to this email for you.</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" ><strong class="slate-bold"  >Booking Details: </strong></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >{{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Location: {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}}</p></td></tr><tr><td><p class="slate-p"  ></p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >Thank you</p></td></tr><tr><td><p class="slate-p" style="padding: 0; margin: 0;" >The <span data-slate-template="true">{{platform_name}}</span> Team </p></td></tr>']
            ],
            ['id'],
            ['text']
        );
    }
}
