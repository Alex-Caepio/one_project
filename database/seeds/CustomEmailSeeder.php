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
                'text' => 'Hi {{first_name}}
Thank you for creating your Client Account on {{platform_name}}. We are extremely excited to welcome you and empower you on your personal transformation journey.
To begin, please verify your email by clicking on the button below or copying and pasting the long URL into your browser: [Verify Email] {{Email Verification URL}}
Thank youThe {{platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Thank you for creating your Practitioner Account on {[platform_name}}. We are extremely excited to welcome you and to empower you in your Business.
You will be able to advertise your business and sell your services and book the services of other practitioners. Here is a guide to help you get started.
To begin, please verify your email by clicking on the button below or copying and pasting the long URL into your browser:
[Verify Email] {{Email Verification URL}} Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} A request has been received to change the password for your {[platform_name}} account.[Reset Password button]
If you did not initiate this request, please contact us immediately at {[platform_email}} Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}}This is to confirm the password for your {[platform_name}} account has been changed.If you did not initiate this change, please contact us immediately at {[platform_email}}
                Thank you The {[platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //5
            [
                'name' => 'Account Deleted',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' =>'{[platform_name}} Account Closed',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} This is to confirm your {[platform_name}} account has now been closed.
If you did not initiate this change, please contact us immediately at {[platform_email}}.Thank youThe {[platform_name}} Team',
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
Thank you for upgrading to a Practitioner Account on {[platform_name}}. We are extremely excited to empower you in your Business.
You will be able to advertise your business and sell your services and still book services of other practitioners. Here is a guide to help you get started.
Your Subscription is {[Subscription Tier Name}}. You can change your subscription plan at any time from your Account section.
[Go to My Account] Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} This is to confirm your {[platform_name}} Practitioner account has now been deleted.
If you did not initiate this change, please contact us immediately at {[platform_email}}.
Thank you The {[platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //8
            [
                'name' => 'Business Profile Live',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Business Profile Live on {[platform_name}}!',
                'logo' => Str::random(7),
                'text' => 'Hi {{first_name}} Congratulations! Your business profile page is live on {[platform_name}} and visible to potential clients. Your website address is:
{{practitioner_URL}} You can add this to your business card, flyers, social media profiles and more!
The next step in gaining new clients is to advertise your services. Here is a guide to help you get started.
[Go to My Services] We are excited to be empowering your business.
Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Congratulations! Your Service Listing {[Service_Name}} is live on {[platform_name}}. The unique website address for this service is: {[Service_URL}} You can use this to promote your service directly on flyers, social media posts and more!
The next step in gaining new clients is to add your Service Schedule if you have not yet done so. Here is a guide to help you do this.
[Go to My Services] We are excited to be empowering your business.
Thank you The {[platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //10
            [
                'name' => 'Service Schedule Live - WS/Event/Physical',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => '{[Service_Name}} Booking Schedule is Live!',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}}
Your Booking Schedule is live for {[Service_Name}} on {[platform_name}} and is ready for Clients to book. The unique website address for this service is:
{[Service_URL}} Make sure to promote it on Social Media to get more bookings! {[Service_Name}} - {{Schedule_Name}} From: {{Schedule_Start_Date}}, {{Schedule_Start_Time}}
To: {{Schedule_End_Date}}, {{Schedule_End_Time}}
Location: {{Schedule_Venue_Name}} {{Schedule_Venue_Address}} {{Schedule_City}}, {[Schedule_Postcode}}, {{Schedule_Country}} [ADD To Calendar] We are excited to be empowering your business.
Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Your Booking Schedule is live for {[Service_Name}} on {[platform_name}} and is ready for Clients to book.
The unique website address for this service is:
{[Service_URL}} Make sure to promote it on Social Media to get more bookings!
{[Service_Name}} - {{Schedule_Name}} From: {{Schedule_Start_Date}}, {{Schedule_Start_Time}}
To: {{Schedule_End_Date}}, {{Schedule_End_Time}} Virtual Location: {{Schedule_Hosting_URL}}
[ADD To Calendar] We are excited to be empowering your business.
Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Your Retreat\'s Booking Schedule is live for {[Service_Name}} on {[platform_name}} and is ready for Clients to book.
The unique website address for this service is: {[Service_URL}} Make sure to promote it on Social Media to get more bookings!
{[Service_Name}} - {{Schedule_Name}} From: {{Schedule_Start_Date}} To: {{Schedule_End_Date}} Location: {{Schedule_City}}, {{Schedule_Country}}
[ADD To Calendar button] We are excited to be empowering your business. Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} As requested, your {[platform_name}} Business Profile page for {[Practitioner_Business_Name}} is now unpublished.
Your Service Listings are also unpublished and you can no longer receive new Client Bookings. If you have existing Client Bookings, you will need to honour them, unless you choose to cancel them.
You can republish Business Profile at any time by going to your Profile Page and clicking the PUBLISH button. [Go to My Profile] Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} This is to confirm {[Service_Name}} is now unpublished on {[platform_name}} and and you can no longer receive new Client Bookings for it.
If you have existing Client Bookings, you will need to honour them, unless you choose to cancel them. You can republish it at any time by going to your Service Listing and clicking the PUBLISH button.
[Go to My Services] Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} {{Schedule_Name}} has now been cancelled for {[Service_Name}}. Thank you The {[platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //16
            [
                'name' => 'Booking Cancelled by Practitioner',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Cancelled –{[Booking Reference}} - {{Practitioner_Business_Name}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Unfortunately, {{Practitioner_Business_Name}} has had to cancel your booking for {[Service_Name}}.You will be refunded fully for any amount you have paid.
Cancelled Service: Booking Reference: {[Booking_Reference}} {[Service_Name}} - {{Schedule_Name}} From: {{Cancelled_Start_Date}}, {{Cancelled_Start_Time}}
To: {{Cancelled_End_Date}}, {{Cancelled_End_Time}} Cost: {{Total_Paid}} Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} You are currently booked with {{Practitioner_Business_Name}} for {[Service_Name}} on the {{Booking_Start_Date}}.
Booking Reference: {[Booking_Reference}} {{Practitioner_Business_Name}} would like to reschedule your booking as follows:
Reschedule Requested:{[Service_Name}} - {{Schedule_Name}} From: {{Reschedule_Start_Date}}, {{Reschedule_Start_Time}} To: {{Reschedule_End_Date}}, {{Reschedule_End_Time}}
Location: {{Reschedule_Venue_Name}}{{Service_Schedule_Reschedule_URL}} {{Reschedule_Venue_Address}} {{Reschedule_City}}, {{Reschedule_Postcode}}, {{Reschedule_Country}}
Message from {{Practitioner_Business_Name}} {[Practitioner_Reschedule_Message}} [Accept Button] [Decline Button] [View My Booking]
You will not be charged for this reschedule. Please note, if you decline or do not respond, the Practitioner may still cancel your booking and if so, you will be refunded.
Current Booking: {[Service_Name}} - {{Schedule_Name}} From: {{Booking_Start_Date}}, {{Booking_Start_Time}}
To: {{Booking_End_Date}}, {{Booking_End_Time}} Location: {{Booking_Venue_Name}}{{Service_Schedule_Booking_URL}} {{Booking_Venue_Address}}
{{Booking_City}}, {{Booking_Postcode}}, {{Booking_Country}} Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} We are pleased to confirm your booking with {{Practitioner_Business_Name}} for {[Service_Name}} has now been changed.
Booking Reference: {[Booking_Reference}} {[Service_Name}} - {{Schedule_Name}} From: {{Reschedule_Start_Date}}, {{Reschedule_Start_Time}}
To: {{Reschedule_End_Date}}, {{Reschedule_End_Time}} Location: {{Reschedule_Venue_Name}}{{Service_Schedule_Reschedule_URL}}
{{Reschedule_Venue_Address}} {{Reschedule_City}}, {{Reschedule_Postcode}}, {{Reschedule_Country}}
[See On Map] [Add To Calendar] [View My Booking]  Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} We are pleased to confirm your Client {{Client_Name}} has accepted the change for {[Service_Name}}.
Booking Reference: {[Booking_Reference}} They are now booked in for: {[Service_Name}} - {{Schedule_Name}}
From: {{Reschedule_Start_Date}}, {{Reschedule_Start_Time}} To: {{Reschedule_End_Date}}, {{Reschedule_End_Time}}
Location: {{Reschedule_Venue_Name}}{{Service_Schedule_Reschedule_URL}} {{Reschedule_Venue_Address}} {{Reschedule_City}}, {{Reschedule_Postcode}}, {{Reschedule_Country}}
Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Unfortunately, your Client {{Client_Name}} has declined the reschedule for {[Service_Name}}.
Their original booking will remain. If you are not able to deliver the booking, you can still cancel it and the client will be refunded. Booking Reference: {[Booking_Reference}}
Current Booking - MAINTAINED {[Service_Name}} - {{Schedule_Name}} From: {{Booking_Start_Date}}, {{Booking_Start_Time}} To: {{Booking_End_Date}}, {{Booking_End_Time}}
Location: {{Booking_Venue_Name}}{{Service_Schedule_Booking_URL}} {{Booking_Venue_Address}} {{Booking_City}}, {{Booking_Postcode}}, {{Booking_Country}}
[View Client Booking] Reschedule - DECLINED {[Service_Name}} - {{Schedule_Name}} From: {{Reschedule_Start_Date}}, {{Reschedule_Start_Time}}
To: {{Reschedule_End_Date}}, {{Reschedule_End_Time}} Location: {{Reschedule_Venue_Name}}{{Service_Schedule_Reschedule_URL}}
{{Reschedule_Venue_Address}} {{Reschedule_City}}, {{Reschedule_Postcode}}, {{Reschedule_Country}}
Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Unfortunately, your Client {{Client_Name}} has not responded to the request to reschedule for {[Service_Name}}.
Their original booking will remain. If you are not able to deliver the booking, you can still cancel it and the client will be refunded. Booking Reference: {[Booking_Reference}}
Current Booking - MAINTAINED {[Service_Name}} - {{Schedule_Name}} From: {{Booking_Start_Date}}, {{Booking_Start_Time}} To: {{Booking_End_Date}}, {{Booking_End_Time}}
Location: {{Booking_Venue_Name}}{{Service_Schedule_Booking_URL}} {{Booking_Venue_Address}} {{Booking_City}}, {{Booking_Postcode}}, {{Booking_Country}}
[View Client Booking] Reschedule - NO RESPONSE {[Service_Name}} - {{Schedule_Name}} From: {{Reschedule_Start_Date}}, {{Reschedule_Start_Time}}
To: {{Reschedule_End_Date}}, {{Reschedule_End_Time}} Location:{{Reschedule_Venue_Name}}{{Service_Schedule_Reschedule_URL}} {{Reschedule_Venue_Address}}
{{Reschedule_City}}, {{Reschedule_Postcode}}, {{Reschedule_Country}} Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} We thought you may like to know that {[Service_Name}} which you have booked with {{Practitioner_Business_Name}} has been updated.
This does not change your Booking which is still as listed below, though the changes may include an updated venue/location details or additional information which may be of interest you.
[View The Service] [View My Booking] Your current booking: {[Service_Name}} - {{Schedule_Name}} Booking Reference: {[Booking_Reference}}
From: {{Booking_Start_Date}}, {{Booking_Start_Time}} To: {{Booking_End_Date}}, {{Booking_End_Time}} Location: {{Booking_Venue_Name}}{{Service_Schedule_Booking_URL}}
{{Booking_Venue_Address}} {{Booking_City}}, {{Booking_Postcode}}, {{Booking_Country}} [See On Map] Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} You are currently booked with {{Practitioner_Business_Name}} for {{Service_Name}}. Booking Reference: {[Booking_Reference}}
{{Practitioner_Business_Name}} has had to change the booking as follows: {[Service_Name}} - {{Schedule_Name}}
From: {{Schedule_Start_Date}}, {{Schedule_Start_Time}} To: {{Schedule_End_Date}}, {{Schedule_End_Time}}
Location: {{Schedule_Venue_Name}}{{Service_Schedule_URL}} {{Schedule_Venue_Address}} {{Schedule_City}}, {[Schedule_Postcode}}, {{Schedule_Country}}
You can either Accept or Decline this change. This will not impact the price you have paid for the service. If you Decline, your booking will be cancelled and you will be refunded in full.
Please note, if you do not reply, this will be considered as accepting the change.  [Accept] [Decline] [View The Service] [View My Booking]
Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Unfortunately, {{Practitioner_Business_Name}} had to change the booking details for {{Service_Name}} - {{Schedule_Name}}.
As you declined the change, your booking has been cancelled. You will be refunded for this booking. Please allow up to 10 days for the refund to reach you.
Cancelled Service: Booking Reference: {[Booking_Reference}} {[Service_Name}} - {{Schedule_Name}} Cost: {{Total_Paid}}
Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Your Booking Schedule {[Schedule_Name}} is live for {[Service_Name}} on {[platform_name}} and is ready for Clients to book.
The unique website address for this service is: {[Service_URL}} Make sure to promote it on Social Media to get more bookings!
We are excited to be empowering your business.Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} {{Practitioner_Business_Name}} will not be able to make your exiting appointment for {{Service_Name}}. Instead they would like to offer you another appointment at a time of your choosing.
You can either Accept this request and book a new time or Decline this request. If you decline or do not respond, the Practitioner may still cancel your current appointment and if so, you will be refunded.
[Accept & Rebook] [Decline] [View My Booking] Message from {{Practitioner_Business_Name}} {[Practitioner_Reschedule_Message}}
Your current booking: {[Service_Name}} - {{Schedule_Name}} Booking Reference: {[Booking_Reference}} From: {{Booking_Start_Date}}, {{Booking_Start_Time}} To: {{Booking_End_Date}}, {{Booking_End_Time}}Location: {{Booking_Venue_Name}}{{Service_Schedule_Booking_URL}} {{Booking_Venue_Address}}
{{Booking_City}}, {{Booking_Postcode}}, {{Booking_Country}} Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} You are currently booked with {{Practitioner_Business_Name}} for {{Service_Name}}, on the {{Booking_Start_Date}}.
Booking Reference: {[Booking_Reference}} [Practitioner Business Name] would like to reschedule your booking as follows:
New Appointment: {[Service_Name}} - {{Schedule_Name}} From: {{Reschedule_Start_Date}}, {{Reschedule_Start_Time}} To: {{Reschedule_End_Date}}, {{Reschedule_End_Time}}
Location: {{Reschedule_Venue_Name}}{{Service_Schedule_Reschedule_URL}} {{Reschedule_Venue_Address}} {{Reschedule_City}}, {{Reschedule_Postcode}}, {{Reschedule_Country}}
Message from {{Practitioner_Business_Name}} {[Practitioner_Reschedule_Message}}
You can either Accept or Decline this request. If you decline or do not respond, the Practitioner may still cancel your current appointment and if so, you will be refunded. [Accept] [Decline] [View My Booking]
Current Booking {[Service_Name}} - {{Schedule_Name}} From: {{Booking_Start_Date}}, {{Booking_Start_Time}}
To: {{Booking_End_Date}}, {{Booking_End_Time}} Location: {{Booking_Venue_Name}}{{Service_Schedule_Booking_URL}} {{Booking_Venue_Address}} {{Booking_City}}, {{Booking_Postcode}}, {{Booking_Country}}
Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Your Schedule {[Schedule_Name}} is live for {[Service_Name}} on {[platform_name}} and is ready for Clients to buy.
The unique website address for this service is: {[Service_URL}} Make sure to promote it on Social Media to get more sales!
We are excited to be empowering your business. Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Unfortunately, {{Practitioner_Business_Name}} has had to cancel your purchase for {[Service_Name}}.
You will be refunded fully for any amount you have paid. Please allow up to 10 days for the refund to reach you. Cancelled Service:
Booking Reference: {[Booking_Reference}} {[Service_Name}} - {{Schedule_Name}} Cost: {{Total_Paid}} Thank you The {[platform_name}} Team',
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
Your booking for {{Service_Name}} with {{Practitioner_Business_Name}} has now been cancelled. You do not need to take any further action as we will advise the practitioner.
You will be refunded, less an admin fee of {{Client_Admin_fee_percent}}%. Please allow up to 10 days for the refund to reach you.  Cancelled Service:
Booking Reference: {[Booking_Reference}} {[Service_Name}} - {{Schedule_Name}} Cost: {{Total_Paid}} Thank you The {[platform_name}} Team',
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
Unfortunately, {[Client_Name}} has had to cancel their booking with you for {{Service_Name}} {{Schedule_Name}}.
Their place will be reopened in your service schedule for resale. As per your cancellation terms, they will be refunded fully for any amount they have paid to date for this service.
Please make sure you have funds are available to cover this refund.Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Your booking for {{Service_Name}} with {{Practitioner_Business_Name}} has now been cancelled. You do not need to take any further action as we will advise the practitioner.
Unfortunately, you will not be refunded based on the cancellation terms set by the Practitioner. Cancelled Service: Booking Reference: {[Booking_Reference}}
{[Service_Name}} - {{Schedule_Name}} Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Unfortunately, {[Client_Name}} has had to cancel their booking with you for {{Service_Name}} {{Schedule_Name}}.
Their place will be reopened in your service schedule for resale. As per your cancellation terms, they will not be refunded for this service.
Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Your Client, {{Client_Name}} has rescheduled their booking for {{Service_Name}}. They are now booked in for:
{[Service_Name}} - {{Schedule_Name}} From: {{Reschedule_Start_Date}}, {{Reschedule_Start_Time}} To: {{Reschedule_End_Date}}, {{Reschedule_End_Time}}
Location: {{Reschedule_Venue_Name}}{{Service_Schedule_Reschedule_URL}} {{Reschedule_Venue_Address}} {{Reschedule_City}}, {{Reschedule_Postcode}}, {{Reschedule_Country}}
 Their original booking will be reopened in your service schedule for resale. Original Booking:
{[Service_Name}} - {{Schedule_Name}} From: {{Booking_Start_Date}}, {{Booking_Start_Time}} To: {{Booking_End_Date}}, {{Booking_End_Time}}
Location: {{Booking_Venue_Name}}{{Service_Schedule_Booking_URL}} {{Booking_Venue_Address}} {{Booking_City}}, {{Booking_Postcode}}, {{Booking_Country}}
Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Your booking for {{Service_Name}} is now confirmed with {{Practitioner_Business_Name}} Booking Details:
{[Service_Name}} - {{Schedule_Name}} Booking Reference: {[Booking_Reference}} Cost: {{Total_Paid}} From: {{Booking_Start_Date}}, {{Booking_Start_Time}}
To: {{Booking_End_Date}}, {{Booking_End_Time}} Location: {{Booking_Venue_Name}} {{Booking_Venue_Address}} {{Booking_City}}, {{Booking_Postcode}} {{Booking_Country}}
[Add To Calendar] [See On Map] Message from {{Practitioner_Business_Name}} {{Practitioner_Booking_Message}} Your Practitioner may have also added some attachments to this email for you.
Thank youThe {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Congratulations! [Client Name] has booked with you for {{Service_Name}}. Booking Details:
{[Service_Name}} - {{Schedule_Name}} Booking Reference: {[Booking_Reference}} Cost: {{Total_Paid}}
From: {{Booking_Start_Date}}, {{Booking_Start_Time}} To: {{Booking_End_Date}}, {{Booking_End_Time}} Location: {{Booking_Venue_Name}} {{Booking_Venue_Address}} {{Booking_City}}, {{Booking_Postcode}} {{Booking_Country}}
[View Client Booking} Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Your Purchase for {{Service_Name}} from {{Practitioner_Business_Name}} is confirmed.
Purchase Details: {[Service_Name}} - {{Schedule_Name}} Booking Reference: {[Booking_Reference}} Cost: {[Total_Paid}} Location: {{Booking_City}}, {{Booking_Country}}
[View My Purchase] Message from {{Practitioner_Business_Name}} {{Practitioner_Booking_Message}}
Your Practitioner may have also added some attachments to this email for you and should also be in touch with you via {[platform_name}} messaging to confirm further details.
Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}}  Congratulations! {{Client_Name}} has purchased {{Service_Name}}. Purchase Details:
{[Service_Name}} - {{Schedule_Name}} Booking Reference: {[Booking_Reference}} Cost: {[Total_Paid}} Location: {{Booking_City}}, {{Booking_Country}}
We recommend getting in touch with {{Client_Name}} directly via {[platform_name}} messaging to welcome them and provide any further information they may need for {{Service_Name}}.
[View Client Purchase}  Thank you The {[platform_name}} Team',
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
Unfortunately, your {[platform_name}} account has been terminated and your existing bookings with Practitioners or from Clients have been cancelled.
Reason for Termination: {[Admin_termination_message}} We are sorry that you will no longer be able to use our platform. If you have any questions, please contact us at [platform email].
Thank you The {[platform_name}} Team',
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
Unfortunately, your {[platform_name}} account has been terminated and your existing bookings with Practitioners have been cancelled. Reason for Termination:
{[Admin_termination_message}} We are sorry that you will no longer be able to use our platform. If you have any questions, please contact us at [platform email].
Thank youThe {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Your booking for {{Service_Name}} is now confirmed with {{Practitioner_Business_Name}} Booking Details:
{[Service_Name}} - {{Schedule_Name}} Booking Reference: {[Booking_Reference}} From: {{Booking_Start_Date}}, {{Booking_Start_Time}} To: {{Booking_End_Date}}, {{Booking_End_Time}}
Location: {{Booking_Venue_Name}} {{Booking_Venue_Address}} {{Booking_City}}, {{Booking_Postcode}} {{Booking_Country}}
[Add To Calendar] [See On Map] Message from {{Practitioner_Business_Name}} {{Practitioner_Booking_Message}} Your Practitioner may have also added some attachments to this email for you. Payment
Deposit Paid: {[Deposit_Paid]} The balance for this service will be charged to your card provided as follows:
{{Instalment_Date_1}} – {{Instalment_Amount_1}}
{{Instalment_Date_2}} – {{Instalment_Amount_2}}
[Etc… for number of Instalments]
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
                'text' => 'Hi {{first_name}} Congratulations! {{Client_Name}}  has booked with you for {{Service_Name}}. Booking Details: {[Service_Name}} - {{Schedule_Name}}
Booking Reference: {[Booking_Reference}} From: {{Booking_Start_Date}}, {{Booking_Start_Time}}
To: {{Booking_End_Date}}, {{Booking_End_Time}} Location: {{Booking_Venue_Name}} {{Booking_Venue_Address}} {{Booking_City}}, {{Booking_Postcode}} {{Booking_Country}}
[View Client Booking] The Client has paid a deposit of {[Deposit_Paid}} and will pay the remaining over instalments as follows:
{{Instalment_Date_1}} – {{Instalment_Amount_1}}
{{Instalment_Date_2}} – {{Instalment_Amount_2}}
[Etc… for number of Instalments]  Thank you The {[platform_name}} Team',
                'delay' => rand(5, 20)
            ],
            //42
            [
                'name' => 'Booking Confirmation - DateLess Physical - with Deposit',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Purchase Confirmation - {[Booking_Reference}} - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your purchase for {{Service_Name}} is now confirmed with {{Practitioner_Business_Name}}
Purchase Details: {[Service_Name}} - {{Schedule_Name}} Booking Reference: {[Booking_Reference}}
Location: {{Booking_City}}, {{Booking_Country}} [View My Purchase] Message from {{Practitioner_Business_Name}}
{{Practitioner_Booking_Message}} Your Practitioner may have also added some attachments to this email for you.
Payment Deposit Paid: {[Deposit_Paid]} The balance for this service will be charged to your card provided as follows:
{{Instalment_Date_1}} – {{Instalment_Amount_1}}
{{Instalment_Date_2}} – {{Instalment_Amount_2}}
[Etc… for number of Instalments]  Please make sure you have funds available for each instalment or your purchase may be cancelled.Thank you The {{platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Congratulations! {{Client_Name}} has purchased {{Service_Name}}.  Purchase Details: {[Service_Name}} - {{Schedule_Name}}
Booking Reference: {[Booking_Reference}} Location: {{Booking_City}}, {{Booking_Country}} [View Client Purchase} The Client has paid a deposit of {[Deposit_Paid]} and will pay the remaining over instalments as follows:
{{Instalment_Date_1}} – {{Instalment_Amount_1}}
{{Instalment_Date_2}} – {{Instalment_Amount_2}}
[Etc… for number of Instalments] Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} This is to remind you that your next instalment payment for {{Service_Name}} from {{Practitioner_Business_Name}} is due in 7 days.  The Instalment Payment Schedule is charged to your card provided as follows:
{{Instalment_Date_1}} – {{Instalment_Amount_1}}
{{Instalment_Date_2}} – {{Instalment_Amount_2}}
[Etc… for number of Instalments] Service: {[Service_Name}} - {{Schedule_Name}} Booking Reference: {[Booking_Reference}} [View My Booking} Thank you
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
                'text' => 'Hi {{first_name}} Your booking for {{Service_Name}} with {{Practitioner_Business_Name}} is just one week away. Booking Details:
{[Service_Name}} - {{Schedule_Name}} Booking Reference: {[Booking_Reference}} From: {{Booking_Start_Date}}, {{Booking_Start_Time}} To: {{Booking_End_Date}}, {{Booking_End_Time}}
Location: {{Booking_Venue_Name}} {{Booking_Venue_Address}} {{Booking_City}}, {{Booking_Postcode}}, {{Booking_Country}} [See On Map] [View My Booking]
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
                'text' => 'Hi {{first_name}} Your Retreat, {{Service_Name}} with {{Practitioner_Business_Name}} is just two weeks away. Booking Details:
{[Service_Name}} - {{Schedule_Name}} Booking Reference: {[Booking_Reference}} From: {{Booking_Start_Date}} To: {{Booking_End_Date}} Location: {{Booking_City}}, {{Booking_Country}}
[View My Booking} Thank you The {{platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Your booking for {{Service_Name}} with {{Practitioner_Business_Name}} is tomorrow.  Booking Details:
{[Service_Name}} - {{Schedule_Name}} Booking Reference: {[Booking_Reference}} From: {{Booking_Start_Date}}, {{Booking_Start_Time}}
To: {{Booking_End_Date}}, {{Booking_End_Time}} Location: {{Booking_Venue_Name}} {{Booking_Venue_Address}} {{Booking_City}}, {{Booking_Postcode}} {{Booking_Country}}
[See On Map] [View My Booking] Thank you The {{platform_name}} Team',
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
                'text' => 'Hi {{first_name}} We are confirming your {[platform_name}}  Subscription Plan has now been changed to {{Subscription_Tier_Name}}, effective from {{Subscription_Start_Date}}
[Go to My Account] We are excited to empower you in your business. Thank you The {[platform_name}}  Team',
                'delay' => rand(5, 20)
            ],
            //49
            [
                'name' => 'Article Published',
                'user_type' => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Your Article is Live on {[platform_name}}!',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Congratulations! Your Article: {{Article_Name}} is live on {[platform_name}}  and visible to potential clients.
 The unique website address is for this service is: {{Article_URL}} Make sure to share it on your Social Media! [Go To My Articles] We are excited to be empowering your business.
 Thank you The {[platform_name}}  Team',
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
                'text' => 'Hi {{first_name}} This is to confirm {{Article_Name}} is now unpublished on {[platform_name}}  and no longer viewable. You can republish it at any time by going to your Article Page and clicking the PUBLISH button.
[Go to My Articles]  Thank you The {[platform_name}}  Team',
                'delay' => rand(5, 20)
            ],
            //51
            [
                'name' => 'Booking Confirmation - Event/Appt Virtual',
                'user_type' => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject' => 'Booking Confirmation - {{Booking_Reference}} - {{Service_Name}}',
                'logo' => Str::random(5),
                'text' => 'Hi {{first_name}} Your booking for {{Service_Name}} is now confirmed with [Practitioner Business Name] Booking Details: {[Service_Name}} - {{Schedule_Name}}
Booking Reference: {{Booking_Reference}} Cost: {{Total_Paid}} From: {{Booking_Start_Date}}, {{Booking_Start_Time}}
To: {{Booking_End_Date}}, {{Booking_End_Time}} Location: {{Service_Schedule_Booking_URL}} [Add To Calendar]  [View My  Booking} Message from {{Practitioner_Business_Name}}
{{Practitioner_Booking_Message}} Your Practitioner may have also added some attachments to this email for you.Thank you The {[platform_name}}  Team',
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
                'text' => 'Hi {{first_name}}  Your Purchase for {{Service_Name}} from {{Practitioner_Business_Name}} is confirmed. Purchase Details: {[Service_Name}} - {{Schedule_Name}}
Order Reference: {{Booking_Reference}} Cost: {{Total_Paid}} Location: {{Service_Schedule_Booking_URL}} [View My  Purchase] Message from {{Practitioner_Business_Name}}
{{Practitioner_Booking_Message}} Your Practitioner may have also added some attachments to this email for you and should be in touch with you via {[platform_name}} email message to confirm further details.
 Thank you The {[platform_name}}  Team',
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
                'text' => 'Hi {{first_name}} Your booking for {{Service_Name}} is now confirmed with {{Practitioner_Business_Name}}. Booking Details:
{[Service_Name}} - {{Schedule_Name}} Booking Reference: {{Booking_Reference}} From: {{Booking_Start_Date}}, {{Booking_Start_Time}}
To: {{Booking_End_Date}}, {{Booking_End_Time}} Location: {{Service_Schedule_Booking_URL}} [Add To Calendar]  [View My  Booking] Message from {{Practitioner_Business_Name}}
{{Practitioner_Booking_Message}} Your Practitioner may have also added some attachments to this email for you. Payment Deposit Paid: {{Deposit_Paid}} The balance for this service will be charged to your card proved as follows:
{{Instalment_Date_1}} – {{Instalment_Amount_1}}
{{Instalment_Date_2}} – {{Instalment_Amount_2}}
[Etc… for number of Instalments] Please make sure you have funds available for each instalment or your Booking may be cancelled. Thank you The {[platform_name}}  Team',
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
                'text' => 'Hi {{first_name}} Welcome to the {[platform_name}}  {{Subscription_Tier_Name}} Subscription Plan. We hope you enjoy using the platform. If you need help at any stage, please contact us at {{platform_email}} or visit the FAQs Your card will be charged a monthly subscription fee of {{subscription_cost}}, and you may be charged for cancellation fee’s if you cancel a Client booking. You can change your subscription at any time from your Account section.
[Go To My Account] We are excited to empower you in your business. Thank you The {[platform_name}}  Team',
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
                'text' => 'Hi {{first_name}} Welcome to the {[platform_name}}  Free Subscription Plan. We hope you enjoy using the features available on your plan. You can also upgrade your subscription at any time from your Account section.
 You will not be charged a monthly subscription fee. Please note, your card may be charged for cancellation fee’s if you cancel a Client booking. [Go To My Account]
 We are excited to empower you in your business. Thank you The {[platform_name}} Team',
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
                'text' => 'Hi {{first_name}} Your booking for {{Service_Name}} is now confirmed with {{Practitioner_Business_Name}}  Purchase Details: {[Service_Name}} - {{Schedule_Name}}
Order Reference: {{Booking_Reference}} Location: {{Service_Schedule_Booking_URL}} [View My  Purchase] Message from {{Practitioner_Business_Name}}
{{Practitioner_Booking_Message}} Your Practitioner may have also added some attachments to this email for you and should also be in touch with you via {[platform_name}} email message to confirm further details.
 Payment Deposit Paid: {{Deposit_Paid}} The balance for this service will be charged to your card proved as follows:
{{Instalment Date}} – {{Instalment Amount}}
{{Instalment Date}} – {{Instalment Amount}}
[Etc… for number of Instalments] Please make sure you have funds available for each instalment or your purchase may be cancelled. Thank you The {[platform_name}}  Team',
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
                'text' => 'Hi {{first_name}} Congratulations! {{Client_Name}} has booked with you for {{Service_Name}}. Booking Details: {[Service_Name}} - {{Schedule_Name}}
Booking Reference: {{Booking_Reference}} Cost: {[Total_Paid}} From: {{Booking_Start_Date}}, {{Booking_Start_Time}} To: {{Booking_End_Date}}, {{Booking_End_Time}}
Location: {{Service_Schedule_Booking_URL}} [View Client Booking] Thank you The {[platform_name}}  Team',
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
                'text' => 'Hi {{first_name}}  Congratulations! {{Client_Name}} has purchased {{Service_Name}}. Purchase Details: {[Service_Name}} - {{Schedule_Name}}
Order Reference: {{Booking_Reference}} Cost: {[Total_Paid}} Location: {{Service_Schedule_Booking_URL}} [View Client Booking]
 We recommend getting in touch with {{Client_Name}} directly via {[platform_name}} email message to welcome them and provide any further information they may need for {{Service_Name}}.
 Thank you The {[platform_name}}  Team',
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
                'text' => 'Hi {{first_name}} Congratulations! {{Client_Name}} has booked with you for {{Service_Name}}. Booking Details: {[Service_Name}} - {{Schedule_Name}}
Booking Reference: {{Booking_Reference}} From: {{Booking_Start_Date}}, {{Booking_Start_Time}} To: {{Booking_End_Date}}, {{Booking_End_Time}}
Location: {{Service_Schedule_Booking_URL}} [View Client Booking] The Client has paid a deposit of {[Deposit_Paid]} and will pay the remaining over instalments as follows:
{{Instalment_Date_1}} – {{Instalment_Amount_1}}
{{Instalment_Date_2}} – {{Instalment_Amount_2}}
[Etc… for number of Instalments] Thank you The {[platform_name}}  Team',
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
                'text' => 'Hi {{first_name}}  Congratulations! {{Client_Name}} has purchased {{Service_Name}}.Purchase Details: {[Service_Name}} - {{Schedule_Name}}
Order Reference: {{Booking_Reference}} Location: {{Service_Schedule_Booking_URL}} [View Client Purchase] The Client has paid a deposit of {[Deposit_Paid]} and will pay the remaining over instalments as follows:
{{Instalment_Date_1}} – {{Instalment_Amount_1}}
{{Instalment_Date_2}} – {{Instalment_Amount_2}}
[Etc… for number of Instalments] Thank you The {[platform_name}}  Team',
                'delay' => rand(5, 20)
            ],
        ];
        DB::table('custom_emails')->insert($data);
    }
}
