<?php

use App\Models\CustomEmail;
use Illuminate\Database\Migrations\Migration;

class UpdateLabelsOfEmailButtons extends Migration
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
                [
                    'id' => 100,
                    'text' => <<<HTML
<tr>
    <td>
        <p class="slate-p"></p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p"></p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p">You are currently booked with <span data-slate-template="true">{{practitioner_business_name}}</span>  for {{service_name}} - {{schedule_name}}.</p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p"></p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p"><span data-slate-template="true">{{practitioner_business_name}}</span><strong class="slate-bold"> has changed this service as follows:</strong></p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p">New start date: {{reschedule_start_date}} at {{reschedule_start_time}}</p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p">New end date: {{reschedule_end_date}} at {{reschedule_end_time}}</p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p">New location: {{reschedule_hosting_url}}{{reschedule_venue_name}} {{reschedule_venue_address}} {{reschedule_city}} {{reschedule_postcode}} {{reschedule_country}}</p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p"></p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p"><strong class="slate-bold">You need to respond to this change</strong></p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p">You can either Accept or Decline this change. </p>
    </td>
</tr>
<tr>
    <td>
        <ul class="slate-ul">
            <li class="slate-li">
                <div data-slate-wrapper="true">If you accept, your booking will be updated with the new details</div>
            </li>
            <li class="slate-li">
                <div data-slate-wrapper="true">If you decline your booking will be cancelled and you will be refunded  any money paid to date</div></li><li class="slate-li"><div data-slate-wrapper="true">If you do not reply by end of day tomorrow, the change will be accepted automatically and you can then cancel your booking if you can&#x27;t attend</div>
            </li>
        </ul>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p"></p>
    </td>
</tr>
<tr>
    <td>
        <a href="{{accept_amend}}" target="_blank" rel="noreferrer" data-button-link="true" style="text-decoration:none"><span style="padding:10px 24px;margin-bottom:8px;color:#ffffff;text-align:center;background-color:#db7a6a;line-height:45px;border-radius:25px;cursor:pointer;user-select:none" contenteditable="false" >Accept Change</span></a>
    </td>
</tr>
<tr>
    <td>
        <a href="{{decline_amend}}" target="_blank" rel="noreferrer" data-button-link="true" style="text-decoration:none"><span style="padding:10px 24px;margin-bottom:8px;color:#ffffff;text-align:center;background-color:#db7a6a;line-height:45px;border-radius:25px;cursor:pointer;user-select:none" contenteditable="false" >Decline Change</span></a>
    </td>
</tr>
<tr>
    <td>
        <a href="{{view_my_booking}}" target="_blank" rel="noreferrer" data-button-link="true" style="text-decoration:none"><span style="padding:10px 24px;margin-bottom:8px;color:#ffffff;text-align:center;background-color:#db7a6a;line-height:45px;border-radius:25px;cursor:pointer;user-select:none" contenteditable="false" >View My Booking</span></a>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p"></p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p">Booking reference: <span data-slate-template="true">{{booking_reference}}</span></p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p">Quantity: <span data-slate-template="true">{{number_of_tickets_purchased}}</span></p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p">This does not impact the price you have paid for the service. </p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p"></p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p">Thank you </p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p">The Holistify Team</p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p"></p>
    </td>
</tr>
<tr>
    <td>
        <p class="slate-p"></p>
    </td>
</tr>
HTML,
                ],
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
                [
                    'id' => 100,
                    'text' => '<tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">You are currently booked with <span data-slate-template="true">{{practitioner_business_name}}</span>  for {{service_name}} - {{schedule_name}}.</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"><span data-slate-template="true">{{practitioner_business_name}}</span><strong class="slate-bold"> has changed this service as follows:</strong></p></td></tr><tr><td><p class="slate-p">New start date: {{reschedule_start_date}} at {{reschedule_start_time}}</p></td></tr><tr><td><p class="slate-p">New end date: {{reschedule_end_date}} at {{reschedule_end_time}}</p></td></tr><tr><td><p class="slate-p">New location: {{reschedule_hosting_url}}{{reschedule_venue_name}} {{reschedule_venue_address}} {{reschedule_city}} {{reschedule_postcode}} {{reschedule_country}}</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"><strong class="slate-bold">You need to respond to this change</strong></p></td></tr><tr><td><p class="slate-p">You can either Accept or Decline this change. </p></td></tr><tr><td><ul class="slate-ul"><li class="slate-li"><div data-slate-wrapper="true">If you accept, your booking will be updated with the new details</div></li><li class="slate-li"><div data-slate-wrapper="true">If you decline your booking will be cancelled and you will be refunded  any money paid to date</div></li><li class="slate-li"><div data-slate-wrapper="true">If you do not reply by end of day tomorrow, the change will be accepted automatically and you can then cancel your booking if you can&#x27;t attend</div></li></ul></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><a href="{{accept_amend}}" target="_blank" rel="noreferrer" data-button-link="true" style="text-decoration:none"><span style="padding:10px 24px;margin-bottom:8px;color:#ffffff;text-align:center;background-color:#db7a6a;line-height:45px;border-radius:25px;cursor:pointer;user-select:none" contenteditable="false" >Accept</span></a></td></tr><tr><td><a href="{{decline_amend}}" target="_blank" rel="noreferrer" data-button-link="true" style="text-decoration:none"><span style="padding:10px 24px;margin-bottom:8px;color:#ffffff;text-align:center;background-color:#db7a6a;line-height:45px;border-radius:25px;cursor:pointer;user-select:none" contenteditable="false" >Decline</span></a></td></tr><tr><td><a href="{{view_my_booking}}" target="_blank" rel="noreferrer" data-button-link="true" style="text-decoration:none"><span style="padding:10px 24px;margin-bottom:8px;color:#ffffff;text-align:center;background-color:#db7a6a;line-height:45px;border-radius:25px;cursor:pointer;user-select:none" contenteditable="false" >View My Booking</span></a></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Booking reference: <span data-slate-template="true">{{booking_reference}}</span> </p></td></tr><tr><td><p class="slate-p">Quantity: <span data-slate-template="true">{{number_of_tickets_purchased}}</span></p></td></tr><tr><td><p class="slate-p">This does not impact the price you have paid for the service. </p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Thank you </p></td></tr><tr><td><p class="slate-p">The Holistify Team</p></td></tr><tr><td><p class="slate-p"></p></td></tr><tr><td><p class="slate-p"></p></td></tr>',
                ],
            ],
            ['id'],
            ['text']
        );
    }
}
