<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $data = [
            //1
            [
                'name' => 'Welcome Verification',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{{platform_name}} Email Verification',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Thank you for creating your Client Account on {{platform_name}}. We are extremely excited to welcome you and empower you on your personal transformation journey.
To begin, please verify your email by clicking on the button below or copying and pasting the long URL into your browser: Verify Email {{email_verification_url}} Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //2
            [
                'name' => 'Welcome Verification',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[platform_name}} Email Verification',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Thank you for creating your Practitioner Account on {{platform_name}}. We are extremely excited to welcome you and to empower you in your Business.
You will be able to advertise your business and sell your services and book the services of other practitioners. Here is a guide to help you get started.
To begin, please verify your email by clicking on the button below or copying and pasting the long URL into your browser:
{{verify_email}} {{email_verification_url}} Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //3
            [
                'name' => 'Password Reset',
                'user_type' => 'all',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[platform_name}} Password Reset',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} A request has been received to change the password for your {{platform_name}} account.{{reset_password_button}}
If you did not initiate this request, please contact us immediately at {{platform_email}} Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //4
            [
                'name' => 'Password Changed',
                'user_type' => 'all',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[platform_name}} Password Changed',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}}This is to confirm the password for your {{platform_name}} account has been changed.If you did not initiate this change, please contact us immediately at {{platform_email}}
                Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //5
            [
                'name' => 'Account Deleted',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[platform_name}} Account Closed',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} This is to confirm your {{platform_name}} account has now been closed.
If you did not initiate this change, please contact us immediately at {{platform_email}}.Thank youThe {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //6
            [
                'name' => 'Account Upgraded to Practitioner',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[platform_name}} Account Upgraded',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}}
Thank you for upgrading to a Practitioner Account on {{platform_name}}. We are extremely excited to empower you in your Business.
You will be able to advertise your business and sell your services and still book services of other practitioners. Here is a guide to help you get started.
Your Subscription is {{subscription_tier_name}}. You can change your subscription plan at any time from your Account section.
Go to My Account Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //7
            [
                'name' => 'Account Deleted',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[platform_name}} Account Closed',
                'logo' => Str::random(6),
                'text' => 'Hi {{first_name}} This is to confirm your {{platform_name}} Practitioner account has now been deleted.
If you did not initiate this change, please contact us immediately at {{platform_email}}.
Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //8
            [
                'name' => 'Business Profile Live',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Business Profile Live on {{platform_name}}!',
                'logo' => Str::random(7),
                'text' => 'Hi {{first_name}} Congratulations! Your business profile page is live on {{platform_name}} and visible to potential clients. Your website address is:
{{practitioner_url}} You can add this to your business card, flyers, social media profiles and more!
The next step in gaining new clients is to advertise your services. Here is a guide to help you get started.
Go to My Services We are excited to be empowering your business.
Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //9
            [
                'name' => 'Service Listing Live',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Service Listing Live on {[platform_name}}!',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Congratulations! Your Service Listing {{service_name}} is live on {{platform_name}}. The unique website address for this service is: {{service_url}} You can use this to promote your service directly on flyers, social media posts and more!
The next step in gaining new clients is to add your Service Schedule if you have not yet done so. Here is a guide to help you do this.
Go to My Services We are excited to be empowering your business.
Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //10ServiceUnpublished
            [
                'name' => 'Service Schedule Live - WS/Event/Physical',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[Service_Name}} Booking Schedule is Live!',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}}
Your Booking Schedule is live for {{service_name}} on {{platform_name}} and is ready for Clients to book. The unique website address for this service is:
{{service_url}} Make sure to promote it on Social Media to get more bookings! {{service_name}} - {{schedule_name}} From: {{schedule_start_date}}, {{schedule_start_time}}
To: {{schedule_end_date}}, {{schedule_end_time}}
Location: {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}} [add_to_calendar] We are excited to be empowering your business.
Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //11
            [
                'name' => 'Service Schedule Live - Event/Virtual',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[Service_Name}} Booking Schedule is Live!',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your Booking Schedule is live for {{service_name}} on {{platform_name}} and is ready for Clients to book.
The unique website address for this service is:
{{service_url}} Make sure to promote it on Social Media to get more bookings!
{{service_name}} - {{schedule_name}} From: {{schedule_start_date}}, {{schedule_start_time}}
To: {{schedule_end_date}}, {{schedule_end_time}} Virtual Location: {{schedule_hosting_url}}
Add to Calendar We are excited to be empowering your business.
Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //12
            [
                'name' => 'Service Schedule Live - Retreat',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Retreat Booking Schedule Live for {[Service_Name}}!',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your Retreat\'s Booking Schedule is live for {{service_name}} on {{platform_name}} and is ready for Clients to book.
The unique website address for this service is: {[Service_URL}} Make sure to promote it on Social Media to get more bookings!
{{service_name}} - {{schedule_name}} From: {{schedule_start_date}} To: {{schedule_end_date}} Location: {{schedule_city}}, {{schedule_country}}
{{add_to_calendar_button}} We are excited to be empowering your business. Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //13
            [
                'name' => 'Business Profile Unpublished',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[platform_name}} Business Profile Unpublished',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} As requested, your {{platform_name}} Business Profile page for {{practitioner_business_name}} is now unpublished.
Your Service Listings are also unpublished and you can no longer receive new Client Bookings. If you have existing Client Bookings, you will need to honour them, unless you choose to cancel them.
You can republish Business Profile at any time by going to your Profile Page and clicking the PUBLISH button. Go to My Profile Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //14
            [
                'name' => 'Service Unpublished',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[Service_Name}} Unpublished',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} This is to confirm {{service_name}} is now unpublished on {{platform_name}} and and you can no longer receive new Client Bookings for it.
If you have existing Client Bookings, you will need to honour them, unless you choose to cancel them. You can republish it at any time by going to your Service Listing and clicking the PUBLISH button.
Go to My Services Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //15
            [
                'name' => 'Service Schedule Cancelled',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Service Schedule Cancelled on {[platform_name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} {{schedule_name}} has now been cancelled for {{service_name}}. Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //16
            [
                'name' => 'Booking Cancelled by Practitioner',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Cancelled – {[Booking Reference}} - {{Practitioner_Business_Name}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Unfortunately, {{practitioner_business_name}} has had to cancel your booking for {{service_name}}.You will be refunded fully for any amount you have paid.
Cancelled Service: Booking Reference: {{booking_reference}} {{service_name}} - {{schedule_name}} From: {{cancelled_start_date}}, {{cancelled_start_time}}
To: {{cancelled_end_date}}, {{cancelled_end_time}} Cost: {{total_paid}} Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //17
            [
                'name' => 'Booking Reschedule Offered by Practitioner - Date',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Reschedule Request – {[Booking_Reference}} - {{Practitioner_Business_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} You are currently booked with {{practitioner_business_name}} for {{service_name}} on the {{booking_start_date}}.
Booking Reference: {{booking_reference}} {{practitioner_business_name}} would like to reschedule your booking as follows:
Reschedule Requested:{{service_name}} - {{schedule_name}} From: {{reschedule_start_date}}, {{reschedule_start_time}} To: {{reschedule_end_date}}, {{reschedule_end_time}}
Location: {{reschedule_venue_name}} {{service_schedule_reschedule_url}} {{reschedule_venue_address}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}
Message from {{practitioner_business_name}} {{practitioner_reschedule_message}} {{accept_button}} {{decline_button}} {{view_my_booking}}
You will not be charged for this reschedule. Please note, if you decline or do not respond, the Practitioner may still cancel your booking and if so, you will be refunded.
Current Booking: {{service_name}} - {{schedule_name}} From: {{booking_start_date}}, {{booking_start_time}}
To: {{booking_end_date}}, {{booking_end_time}} Location: {{booking_venue_name}} {{service_schedule_booking_url}} {{booking_venue_address}}
{{booking_city}}, {{booking_postcode}}, {{booking_country}} Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //18
            [
                'name' => 'Booking Reschedule Accepted by Client',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Change Confirmation - {[Booking_Reference}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} We are pleased to confirm your booking with {{practitioner_business_name}} for {{service_name}} has now been changed.
Booking Reference: {{booking_reference}} {{service_name}} - {{schedule_name}} From: {{reschedule_start_date}}, {{reschedule_start_time}}
To: {{reschedule_end_date}}, {{reschedule_end_time}} Location: {{reschedule_venue_name}} {{service_schedule_reschedule_url}}
{{reschedule_venue_address}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}
{{see_on_map}} {{add_to_calendar}} {{view_my_booking}}  Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //19
            [
                'name' => 'Booking Reschedule Accepted by Client',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Change Confirmation - {[Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} We are pleased to confirm your Client {{client_name}} has accepted the change for {{service_name}}.
Booking Reference: {{booking_reference}} They are now booked in for: {{service_name}} - {{schedule_name}}
From: {{reschedule_start_date}}, {{reschedule_start_time}} To: {{reschedule_end_date}}, {{reschedule_end_time}}
Location: {{reschedule_venue_name}} {{service_schedule_reschedule_url}} {{reschedule_venue_address}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}
Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //20
            [
                'name' => 'Reschedule Request Declined by Client',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Reschedule Declined - {[Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Unfortunately, your Client {{client_name}} has declined the reschedule for {{service_name}}.
Their original booking will remain. If you are not able to deliver the booking, you can still cancel it and the client will be refunded. Booking Reference: {{booking_reference}}
Current Booking - MAINTAINED {{service_name}} - {{schedule_name}} From: {{booking_start_date}}, {{booking_start_time}} To: {{booking_end_date}}, {{booking_end_time}}
Location: {{booking_venue_name}} {{service_schedule_booking_url}} {{booking_venue_address}} {{booking_city}}, {{booking_postcode}}, {{booking_country}}
{{view_client_booking}} Reschedule - DECLINED {{service_name}} - {{schedule_name}} From: {{reschedule_start_date}}, {{reschedule_start_time}}
To: {{reschedule_end_date}}, {{reschedule_end_time}} Location: {{reschedule_venue_name}} {{service_schedule_reschedule_url}}
{{reschedule_venue_address}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}
Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //21
            [
                'name' => 'Reschedule Request No Reply from Client',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Reschedule Not Confirmed - {[Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Unfortunately, your Client {{client_name}} has not responded to the request to reschedule for {{service_name}}.
Their original booking will remain. If you are not able to deliver the booking, you can still cancel it and the client will be refunded. Booking Reference: {{booking_reference}}
Current Booking - MAINTAINED {{service_name}} - {{schedule_name}} From: {{booking_start_date}}, {{booking_start_time}} To: {{booking_end_date}}, {{booking_end_time}}
Location: {{booking_venue_name}} {{service_schedule_booking_url}} {{booking_venue_address}} {{booking_city}}, {{booking_postcode}}, {{booking_country}}
{{view_client_booking}} Reschedule - NO RESPONSE {{service_name}} - {{schedule_name}} From: {{reschedule_start_date}}, {{reschedule_start_time}}
To: {{reschedule_end_date}}, {{reschedule_end_time}} Location:{{reschedule_venue_name}} {{service_schedule_reschedule_url}} {{reschedule_venue_address}}
{{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}} Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //22
            [
                'name' => 'Service Updated by Practitioner (Non-Contractual)',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'A Service You Have Booked is Updated - {[Booking_Reference}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} We thought you may like to know that {{service_name}} which you have booked with {{practitioner_business_name}} has been updated.
This does not change your Booking which is still as listed below, though the changes may include an updated venue/location details or additional information which may be of interest you.
{{view_the_service}} {{view_my_booking] Your current booking: {{service_name}} - {{schedule_name}} Booking Reference: {{booking_reference}}
From: {{booking_start_date}}, {{booking_start_time}} To: {{booking_end_date}}, {{booking_end_time}} Location: {{booking_venue_name}} {{service_schedule_booking_url}}
{{booking_venue_address}} {{booking_city}}, {{booking_postcode}}, {{booking_country}} {{see_on_map}} Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //23
            [
                'name' => 'Service Updated by Practitioner (Contractual)',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Important! Your Booking Has Been Changed – {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} You are currently booked with {{practitioner_business_name}} for {{service_name}}. Booking Reference: {{booking_reference}}
{{practitioner_business_name}} has had to change the booking as follows: {{service_name}} - {{schedule_name}}
From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}
Location: {{schedule_venue_name}} {{service_schedule_url}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}
You can either Accept or Decline this change. This will not impact the price you have paid for the service. If you Decline, your booking will be cancelled and you will be refunded in full.
Please note, if you do not reply, this will be considered as accepting the change.  {{accept}} {{decline}} {{view_the_service}} {{view_my_booking}}
Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //24
            [
                'name' => 'Contractual Service Update Declined - Booking Cancelled',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Cancelled - {[Booking_Reference}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Unfortunately, {{practitioner_business_name}} had to change the booking details for {{service_name}} - {{schedule_name}}.
As you declined the change, your booking has been cancelled. You will be refunded for this booking. Please allow up to 10 days for the refund to reach you.
Cancelled Service: Booking Reference: {{booking_reference}} {{service_name}} - {{schedule_name}} Cost: {{total_paid}}
Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //25
            [
                'name' => 'Service Schedule Live - Appointments',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{{Service_Name}} Schedule is Live!',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your Booking Schedule {{schedule_name}} is live for {{service_name}} on {{platform_name}} and is ready for Clients to book.
The unique website address for this service is: {{service_url}} Make sure to promote it on Social Media to get more bookings!
We are excited to be empowering your business.Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //26
            [
                'name' => 'Booking Reschedule Client to Select - Appt',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Reschedule Request – {{Practitioner_Business_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} {{practitioner_business_name}} will not be able to make your exiting appointment for {{service_name}}. Instead they would like to offer you another appointment at a time of your choosing.
You can either Accept this request and book a new time or Decline this request. If you decline or do not respond, the Practitioner may still cancel your current appointment and if so, you will be refunded.
{{accept_rebook}} {{decline}} {{view_my_booking}} Message from {{practitioner_business_name}} {{practitioner_reschedule_message}}
Your current booking: {{service_name}} - {{schedule_name}} Booking Reference: {{booking_reference}} From: {{booking_start_date}}, {{booking_start_time}} To: {{booking_end_date}}, {{booking_end_time}} Location: {{booking_venue_name}} {{service_schedule_booking_url}} {{booking_venue_address}}
{{booking_city}}, {{booking_postcode}}, {{booking_country}} Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //27
            [
                'name' => 'Booking Reschedule Offered by Practitioner - Appt',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Reschedule Request – {{Practitioner_Business_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} You are currently booked with {{practitioner_business_name}} for {{service_name}}, on the {{booking_start_date}}.
Booking Reference: {{booking_reference}} {{practitioner_business_name}} would like to reschedule your booking as follows:
New Appointment: {{service_name}} - {{schedule_name}} From: {{reschedule_start_date}}, {{reschedule_start_time}} To: {{reschedule_end_date}}, {{reschedule_end_time}}
Location: {{reschedule_venue_name}} {{service_schedule_reschedule_url}} {{reschedule_venue_address}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}
Message from {{practitioner_business_name}} {{practitioner_reschedule_message}}
You can either Accept or Decline this request. If you decline or do not respond, the Practitioner may still cancel your current appointment and if so, you will be refunded. {{accept}} {{decline}} {{view_my_booking}}
Current Booking {{service_name}} - {{schedule_name}} From: {{booking_start_date}}, {{booking_start_time}}
To: {{booking_end_date}}, {{booking_end_time}} Location: {{booking_venue_name}} {{service_schedule_booking_url}} {{booking_venue_address}} {{booking_city}}, {{booking_postcode}}, {{booking_country}}
Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //28
            [
                'name' => 'Service Schedule Live - Date-less',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{{Service_Name}} Ready for Purchase on {[platform_name}}!',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your Schedule {{schedule_name}} is live for {{service_name}} on {{platform_name}} and is ready for Clients to buy.
The unique website address for this service is: {{service_url}} Make sure to promote it on Social Media to get more sales!
We are excited to be empowering your business. Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //29
            [
                'name' => 'Purchase Cancelled by Practitioner',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Purchase Cancelled – {[Booking Reference}} - {{Practitioner_Business_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Unfortunately, {{practitioner_business_name}} has had to cancel your purchase for {{service_name}}.
You will be refunded fully for any amount you have paid. Please allow up to 10 days for the refund to reach you. Cancelled Service:
Booking Reference: {{booking_reference}} {{service_name}} - {{schedule_name}} Cost: {{total_paid}} Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //30
            [
                'name' => 'Booking Cancelled by Client with Refund',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Cancelled – {[Booking_Reference}} - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}}
Your booking for {{service_name}} with {{practitioner_business_name}} has now been cancelled. You do not need to take any further action as we will advise the practitioner.
You will be refunded, less an admin fee of {{client_admin_fee_percent}}%. Please allow up to 10 days for the refund to reach you.  Cancelled Service:
Booking Reference: {{booking_reference}} {{service_name}} - {{schedule_name}} Cost: {{total_paid}} Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //31
            [
                'name' => 'Booking Cancelled by Client with Refund',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Cancelled – {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}}
Unfortunately, {{client_name}} has had to cancel their booking with you for {{service_name}} {{schedule_name}}.
Their place will be reopened in your service schedule for resale. As per your cancellation terms, they will be refunded fully for any amount they have paid to date for this service.
Please make sure you have funds are available to cover this refund.Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //32
            [
                'name' => 'Booking Cancelled by Client NO Refund',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Cancelled – {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your booking for {{service_name}} with {{practitioner_business_name}} has now been cancelled. You do not need to take any further action as we will advise the practitioner.
Unfortunately, you will not be refunded based on the cancellation terms set by the Practitioner. Cancelled Service: Booking Reference: {[booking_reference}}
{{service_name}} - {{schedule_name}} Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //32
            [
                'name' => 'Booking Cancelled by Client NO Refund',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Cancelled – {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Unfortunately, {{client_name}} has had to cancel their booking with you for {{service_name}} {{schedule_name}}.
Their place will be reopened in your service schedule for resale. As per your cancellation terms, they will not be refunded for this service.
Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //33
            [
                'name' => 'Client Rescheduled FYI',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Rescheduled - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your Client, {{client_name}} has rescheduled their booking for {{service_name}}. They are now booked in for:
{{service_name}} - {{schedule_name}} From: {{reschedule_start_date}}, {{reschedule_start_time}} To: {{reschedule_end_date}}, {{reschedule_end_time}}
Location: {{reschedule_venue_name}} {{service_schedule_reschedule_url}} {{reschedule_venue_address}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}
 Their original booking will be reopened in your service schedule for resale. Original Booking:
{{service_name}} - {{schedule_name}} From: {{booking_start_date}}, {{booking_start_time}} To: {{booking_end_date}}, {{booking_end_time}}
Location: {{booking_venue_name}} {{service_schedule_booking_url}} {{booking_venue_address}} {{booking_city}}, {{booking_postcode}}, {{booking_country}}
Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //34
            [
                'name' => 'Booking Confirmation - Date/Apt Physical',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Confirmation - {[Booking_Reference}} - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}} Booking Details:
{{service_name}} - {{schedule_name}} Booking Reference: {{booking_reference}} Cost: {{total_paid}} From: {{booking_start_date}}, {{booking_start_time}}
To: {{booking_end_date}}, {{booking_end_time}} Location: {{booking_venue_name}} {{booking_venue_address}} {{booking_city}}, {{booking_postcode}} {{booking_country}}
{{add_to_calendar}} {{see_on_map}} Message from {{practitioner_business_name}} {{practitioner_booking_message}} Your Practitioner may have also added some attachments to this email for you.
Thank youThe {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //35
            [
                'name' => 'Booking Confirmation - Date/Apt Physical',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Confirmation - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Congratulations! {{client_name}} has booked with you for {{service_name}}. Booking Details:
{{service_name}} - {{schedule_name}} Booking Reference: {{booking_reference}} Cost: {{total_paid}}
From: {{booking_start_date}}, {{booking_start_time}} To: {{booking_end_date}}, {{booking_end_time}} Location: {{booking_venue_name}} {{booking_venue_address}} {{booking_city}}, {{booking_postcode}} {{booking_country}}
{{view_client_booking}} Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //36
            [
                'name' => 'Booking Confirmation - Dateless Physical',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Purchase Confirmation - {[Booking_Reference}} - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your Purchase for {{service_name}} from {{practitioner_business_name}} is confirmed.
Purchase Details: {{service_name}} - {{schedule_name}} Booking Reference: {{booking_reference}} Cost: {{total_paid}} Location: {{booking_city}}, {{booking_country}}
{{view_my_purchase}} Message from {{practitioner_business_name}} {{practitioner_booking_message}}
Your Practitioner may have also added some attachments to this email for you and should also be in touch with you via {[platform_name}} messaging to confirm further details.
Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //37
            [
                'name' => 'Booking Confirmation - Date-Less Physical',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Purchase Confirmation - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}}  Congratulations! {{client_name}} has purchased {{service_name}}. Purchase Details:
{{service_name}} - {{schedule_name}} Booking Reference: {{booking_reference}} Cost: {{total_paid}} Location: {{booking_city}}, {{booking_country}}
We recommend getting in touch with {{client_name}} directly via {{platform_name}} messaging to welcome them and provide any further information they may need for {{service_name}}.
{{view_client_purchase}}  Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //38
            [
                'name' => 'Account Terminated by Admin',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[platform_name}} Account Termination',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}}
Unfortunately, your {{platform_name}} account has been terminated and your existing bookings with Practitioners or from Clients have been cancelled.
Reason for Termination: {{admin_termination_message}} We are sorry that you will no longer be able to use our platform. If you have any questions, please contact us at {{platform_email}}.
Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //39
            [
                'name' => 'Account Terminated by Admin',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[platform_name}} Account Termination',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}}
Unfortunately, your {{platform_name}} account has been terminated and your existing bookings with Practitioners have been cancelled. Reason for Termination:
{{admin_termination_message}} We are sorry that you will no longer be able to use our platform. If you have any questions, please contact us at {{platform_email}}.
Thank youThe {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //40
            [
                'name' => 'Booking Confirmation - Date Physical - with Deposit',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Confirmation - {[Booking_Reference}} - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}} Booking Details:
{{service_name}} - {{schedule_name}} Booking Reference: {{booking_reference}} From: {{booking_start_date}}, {{booking_start_time}} To: {{booking_end_date}}, {{booking_end_time}}
Location: {{booking_venue_name}} {{booking_venue_address}} {{booking_city}}, {{booking_postcode}} {{booking_country}}
{{add_to_calendar}} {{see_on_map}} Message from {{practitioner_business_name}} {{practitioner_booking_message}} Your Practitioner may have also added some attachments to this email for you. Payment
Deposit Paid: {{deposit_paid]} The balance for this service will be charged to your card provided as follows:
{{instalment_date_1}} – {{instalment_amount_1}}
{{instalment_date_2}} – {{instalment_amount_2}}
{{etc_for_number_of_instalments}}
Please make sure you have funds available for each instalment or your Booking may be cancelled. Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //41
            [
                'name' => 'Booking Confirmation - Date Physical - with Deposit',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Confirmation - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Congratulations! {{client_name}}  has booked with you for {{service_name}}. Booking Details: {{service_name}} - {{schedule_name}}
Booking Reference: {{booking_reference}} From: {{booking_start_date}}, {{booking_start_time}}
To: {{booking_end_date}}, {{booking_end_time}} Location: {{booking_venue_name}} {{booking_venue_address}} {{booking_city}}, {{booking_postcode}} {{booking_country}}
{{view_client_booking}} The Client has paid a deposit of {{deposit_paid}} and will pay the remaining over instalments as follows:
{{instalment_date_1}} – {{instalment_amount_1}}
{{instalment_date_2}} – {{instalment_amount_2}}
{{etc_for_number_of_instalments}}  Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //42
            [
                'name' => 'Booking Confirmation - DateLess Physical - with Deposit',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Purchase Confirmation - {{booking_reference}} - {{service_name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your purchase for {{service_name}} is now confirmed with {{practitioner_business_name}}
Purchase Details: {{service_name}} - {{schedule_name}} Booking Reference: {{booking_reference}}
Location: {{booking_city}}, {{booking_country}} {{view_my_purchase}} Message from {{practitioner_business_name}}
{{practitioner_booking_message}} Your Practitioner may have also added some attachments to this email for you.
Payment Deposit Paid: {{deposit_paid}} The balance for this service will be charged to your card provided as follows:
{{instalment_date_1}} – {{instalment_amount_1}}
{{instalment_date_2}} – {{instalment_amount_2}}
{{etc_for_number_of_instalments}}  Please make sure you have funds available for each instalment or your purchase may be cancelled.Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //43
            [
                'name' => 'Booking Confirmation - DateLess Physical - with Deposit',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Purchase Confirmation - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Congratulations! {{client_name}} has purchased {{service_name}}.  Purchase Details: {{service_name}} - {{schedule_name}}
Booking Reference: {{booking_reference}} Location: {{booking_city}}, {{booking_country}} {{view_client_purchase}} The Client has paid a deposit of {{deposit_paid]} and will pay the remaining over instalments as follows:
{{instalment_date_1}} – {{instalment_amount_1}}
{{instalment_date_2}} – {{instalment_amount_2}}
{{etc_for_number_of_instalments}} Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //44
            [
                'name' => 'Instalment Payment Reminder',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Payment Reminder {[Booking_Reference}} - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} This is to remind you that your next instalment payment for {{service_name}} from {{practitioner_business_Name}} is due in 7 days.  The Instalment Payment Schedule is charged to your card provided as follows:
{{instalment_date_1}} – {{instalment_amount_1}}
{{instalment_date_2}} – {{instalment_amount_2}}
{{etc_for_number_of_instalments}} Service: {{service_name}} - {{schedule_name}} Booking Reference: {{booking_reference}} {{view_my_booking}} Thank you
The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //45
            [
                'name' => 'Booking Reminder - WS/Event',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '1 Week to Go - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your booking for {{service_name}} with {{practitioner_business_name}} is just one week away. Booking Details:
{{service_name}} - {{schedule_name}} Booking Reference: {{booking_reference}} From: {{booking_start_date}}, {{booking_start_time}} To: {{booking_end_date}}, {{booking_end_time}}
Location: {{booking_venue_name}} {{booking_venue_address}} {{booking_city}}, {{booking_postcode}}, {{booking_country}} {{see_on_map}} {{view_my_booking}}
Thank you The {{platform_name}} Team ',
                'delay' => rand(5, 20)
            ],
            //46
            [
                'name' => 'Booking Reminder - Retreat',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '2 Weeks to Go - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your Retreat, {{service_name}} with {{practitioner_business_name}} is just two weeks away. Booking Details:
{{service_name}} - {{schedule_name}} Booking Reference: {{booking_reference}} From: {{booking_start_date}} To: {{booking_end_date}} Location: {{booking_city}}, {{booking_country}}
{{view_my_booking}} Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //47
            [
                'name' => 'Booking Reminder - WS/Event Retreat/Appointment',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Tomorrow - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your booking for {{service_name}} with {{practitioner_business_name}} is tomorrow.  Booking Details:
{{service_name}} - {{schedule_name}} Booking Reference: {{booking_reference}} From: {{booking_start_date}}, {{booking_start_time}}
To: {{booking_end_date}}, {{booking_end_time}} Location: {{booking_venue_name}} {{booking_venue_address}} {{booking_city}}, {{booking_postcode}} {{booking_country}}
{{see_on_map}} {{view_my_booking}} Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //48
            [
                'name' => 'Change of Subscription',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[platform_name}} Subscription Plan Changed',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} We are confirming your {{platform_name}}  Subscription Plan has now been changed to {{subscription_tier_name}}, effective from {{subscription_start_date}}
Go to My Articles  We are excited to empower you in your business. Thank you The {{platform_name}}  Team',
                'delay' => rand(5, 20)
            ],
            //49
            [
                'name' => 'Article Published',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Your Article is Live on {{platform_name}}!',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Congratulations! Your Article: {{article_name}} is live on {{platform_name}}  and visible to potential clients.
 The unique website address is for this service is: {{article_url}} Make sure to share it on your Social Media! Go to My Articles We are excited to be empowering your business.
 Thank you The {{platform_name}}  Team',
                'delay' => rand(5, 20)
            ],
            //50
            [
                'name' => 'Article Unpublished',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{{Article_Name}} Unpublished',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} This is to confirm {{article_name}} is now unpublished on {{platform_name}}  and no longer viewable. You can republish it at any time by going to your Article Page and clicking the PUBLISH button.
Go to My Articles  Thank you The {{platform_name}}  Team',
                'delay' => rand(5, 20)
            ],
            //51
            [
                'name' => 'Booking Confirmation - Event/Appt Virtual',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Confirmation - {{booking_reference}} - {{service_name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}} Booking Details: {{service_name}} - {{schedule_name}}
Booking Reference: {{booking_reference}} Cost: {{total_paid}} From: {{booking_start_date}}, {{booking_start_time}}
To: {{booking_end_date}}, {{booking_end_time}} Location: {{service_schedule_booking_url}} {{add_to_calendar}}  {{view_my_booking}} Message from {{practitioner_business_name}}
{{practitioner_booking_message}} Your Practitioner may have also added some attachments to this email for you.Thank you The {{platform_name}}  Team',
                'delay' => rand(5, 20)
            ],
            //52
            [
                'name' => 'Booking Confirmation - DateLess Virtual',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Order Confirmation - {{Booking_Reference}} - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}}  Your Purchase for {{service_name}} from {{practitioner_business_name}} is confirmed. Purchase Details: {{service_name}} - {{schedule_name}}
Order Reference: {{booking_reference}} Cost: {{total_paid}} Location: {{service_schedule_booking_url}} {{view_my_purchase}} Message from {{practitioner_business_name}}
{{practitioner_booking_message}} Your Practitioner may have also added some attachments to this email for you and should be in touch with you via {{platform_name}} email message to confirm further details.
 Thank you The {{platform_name}}  Team',
                'delay' => rand(5, 20)
            ],
            //53
            [
                'name' => 'Booking Confirmation - Event Virtual With Deposit',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Confirmation - {{Booking_Reference}} - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}. Booking Details:
{{service_name}} - {{schedule_name}} Booking Reference: {{booking_reference}} From: {{booking_start_date}}, {{booking_start_time}}
To: {{booking_end_date}}, {{booking_end_time}} Location: {{service_schedule_booking_url}} {{add_to_calendar}}  {{view_my_booking}} Message from {{practitioner_business_name}}
{{practitioner_booking_message}} Your Practitioner may have also added some attachments to this email for you. Payment Deposit Paid: {{deposit_paid}} The balance for this service will be charged to your card proved as follows:
{{instalment_date_1}} – {{instalment_amount_1}}
{{instalment_date_2}} – {{instalment_amount_2}}
{{etc_for_number_of_instalments}}  Please make sure you have funds available for each instalment or your Booking may be cancelled. Thank you The {[platform_name}}  Team',
                'delay' => rand(5, 20)
            ],
            //54
            [
                'name' => 'Subscription confirmation - Paid',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[platform_name}} - {{Subscription_Tier_Name}} Subscription',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Welcome to the {{platform_name}}  {{subscription_tier_name}} Subscription Plan. We hope you enjoy using the platform. If you need help at any stage, please contact us at {{platform_email}} or visit the FAQs Your card will be charged a monthly subscription fee of {{subscription_cost}}, and you may be charged for cancellation fee’s if you cancel a Client booking. You can change your subscription at any time from your Account section.
Go to My Account We are excited to empower you in your business. Thank you The {{platform_name}}  Team',
                'delay' => rand(5, 20)
            ],
            //55
            [
                'name' => 'Subscription confirmation - Free',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[platform_name}}  Free Subscription',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Welcome to the {{platform_name}}  Free Subscription Plan. We hope you enjoy using the features available on your plan. You can also upgrade your subscription at any time from your Account section.
 You will not be charged a monthly subscription fee. Please note, your card may be charged for cancellation fee’s if you cancel a Client booking. Go to My Account
 We are excited to empower you in your business. Thank you The {{platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //56
            [
                'name' => 'Booking Confirmation - DateLess Virtual with Deposit',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Order Confirmation - {{Booking_Reference}} - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}  Purchase Details: {{service_name}} - {{schedule_name}}
Order Reference: {{booking_reference}} Location: {{service_schedule_booking_url}} {{view_my_purchase}} Message from {{practitioner_business_name}}
{{practitioner_booking_message}} Your Practitioner may have also added some attachments to this email for you and should also be in touch with you via {{platform_name}} email message to confirm further details.
 Payment Deposit Paid: {{deposit_paid}} The balance for this service will be charged to your card proved as follows:
{{instalment_date_1}} – {{instalment_amount_1}}
{{instalment_date_2}} – {{instalment_amount_2}}
{{etc_for_number_of_instalments}} Please make sure you have funds available for each instalment or your purchase may be cancelled. Thank you The {{platform_name}}  Team',
                'delay' => rand(5, 20)
            ],
            //57
            [
                'name' => 'Booking Confirmation - Event Virtual',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Confirmation - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Congratulations! {{client_name}} has booked with you for {{service_name}}. Booking Details: {{service_name}} - {{schedule_name}}
Booking Reference: {{booking_reference}} Cost: {{total_paid}} From: {{booking_start_date}}, {{booking_start_time}} To: {{booking_end_date}}, {{booking_end_time}}
Location: {{service_schedule_booking_url}} {{view_client_booking}} Thank you The {{platform_name}}  Team',
                'delay' => rand(5, 20)
            ],
            //58
            [
                'name' => 'Booking  Confirmation Dateless Virtual',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Purchase Confirmation - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}}  Congratulations! {{client_name}} has purchased {{service_name}}. Purchase Details: {{service_name}} - {{schedule_name}}
Order Reference: {{booking_reference}} Cost: {{total_paid}} Location: {{service_schedule_booking_url}} {{view_client_booking}}
 We recommend getting in touch with {{client_name}} directly via {{platform_name}} email message to welcome them and provide any further information they may need for {{service_name}}.
 Thank you The {{platform_name}}  Team',
                'delay' => rand(5, 20)
            ],
            //59
            [
                'name' => 'Booking Event Virtual - with Deposit',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Confirmation - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Congratulations! {{client_name}} has booked with you for {{service_name}}. Booking Details: {{service_name}} - {{schedule_name}}
Booking Reference: {{booking_reference}} From: {{booking_start_date}}, {{booking_start_time}} To: {{booking_end_date}}, {{booking_end_time}}
Location: {{service_schedule_booking_url}} {{view_client_booking}} The Client has paid a deposit of {{deposit_paid]} and will pay the remaining over instalments as follows:
{{instalment_date_1}} – {{instalment_amount_1}}
{{instalment_date_2}} – {{instalment_amount_2}}
{{etc_for_number_of_instalments}} Thank you The {{platform_name}}  Team',
                'delay' => rand(5, 20)
            ],
            //60
            [
                'name' => 'Booking Confirmation Dateless Virtual – with Deposit',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Purchase Confirmation - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}}  Congratulations! {{client_name}} has purchased {{service_name}}.Purchase Details: {{service_name}} - {{schedule_name}}
Order Reference: {{booking_reference}} Location: {{service_schedule_booking_url}} {{view_client_purchase}} The Client has paid a deposit of {{deposit_paid]} and will pay the remaining over instalments as follows:
{{instalment_date_1}} – {{instalment_amount_1}}
{{instalment_date_2}} – {{instalment_amount_2}}
{{etc_for_number_of_instalments}} Thank you The {{platform_name}}  Team',
                'delay' => rand(5, 20)
            ],
        ];
        DB::table('custom_emails')->insert($data);
    }
}
