<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomEmailSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */

    public function run() {
        DB::table('custom_emails')->delete();
        $data = [
            //1
            [
                'name'       => 'Welcome Verification',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{platform_name}} Email Verification',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>
Thank you for creating your Client Account on {{platform_name}}. We are extremely excited to welcome you and empower you on your personal transformation journey. <br/>
 <br/>
To begin, please verify your email by clicking on the button below or copying and pasting the long URL into your browser:  <br/>
<a href="{{email_verification_url}}">Verify Email</a> <br/><br/>
Thank you <br/>
The {{platform_name}} Team <br/>',
                'delay'      => random_int(5, 20)
            ],
            //2
            [
                'name'       => 'Welcome Verification',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{platform_name}} Email Verification',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>
Thank you for creating your Practitioner Account on {{platform_name}}. We are extremely excited to welcome you and to empower you in your Business.<br/>
<br/>
You will be able to advertise your business and sell your services and book the services of other practitioners. Here is a guide to help you get started.<br/>
To begin, please verify your email by clicking on the button below or copying and pasting the long URL into your browser:<br/>
<a href="{{email_verification_url}}">Verify Email</a> <br/>
<br/>
Thank you <br/>
The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //3
            [
                'name'       => 'Password Reset',
                'user_type'  => 'all',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{platform_name}} Password Reset',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
A request has been received to change the password for your {{platform_name}} account.<br/>
<a href="{{reset_password_url}}" target="_blank">Reset Password</a><br/>
If you did not initiate this request, please contact us immediately at {{platform_email}}.<br/><br/>
Thank you<br/>
The {{platform_name}} Team <br/>',
                'delay'      => random_int(5, 20)
            ],
            //4
            [
                'name'       => 'Password Changed',
                'user_type'  => 'all',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{platform_name}} Password Changed',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
This is to confirm the password for your {{platform_name}} account has been changed.
If you did not initiate this change, please contact us immediately at {{platform_email}}.<br/><br/>
                Thank you<br/> The {{platform_name}} Team <br/>',
                'delay'      => random_int(5, 20)
            ],
            //5
            [
                'name'       => 'Account Deleted',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{platform_name}} Account Closed',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
This is to confirm your {{platform_name}} account has now been closed.
If you did not initiate this change, please contact us immediately at {{platform_email}}.<br/><br/>
Thank you<br/>
The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //6
            [
                'name'       => 'Account Deleted',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{platform_name}} Account Closed',
                'logo'       => Str::random(6),
                'text'       => 'Hi {{first_name}}<br/>
This is to confirm your {{platform_name}} Practitioner account has now been deleted.
If you did not initiate this change, please contact us immediately at {{platform_email}}.<br/><br/>
Thank you<br/>
The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //7
            [
                'name'       => 'Account Upgraded to Practitioner',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{platform_name}} Account Upgraded',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
Thank you for upgrading to a Practitioner Account on {{platform_name}}.<br/>
We are extremely excited to empower you in your Business. You will be able to advertise your business and sell your
services and still book services of other practitioners. Here is a guide to help you get started.<br/>
Your Subscription is {{subscription_tier_name}}. You can change your subscription plan at any time from your Account section.
<br /><a href="{{my_account}}" target="_blank">Go To My Account</a><br /><br/>
 Thank you<br/>
 The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //8
            [
                'name'       => 'Business Profile Live',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Business Profile Live on {{platform_name}}!',
                'logo'       => Str::random(7),
                'text'       => 'Hi {{first_name}}<br/>
Congratulations! Your business profile page is live on {{platform_name}} and visible to potential clients. Your website address is:
<a href="{{practitioner_url}}" target="_blank">Your business address</a>. You can add this to your business card, flyers, social media profiles and more!
The next step in gaining new clients is to advertise your services. Here is a guide to help you get started.<br/>
<br /><a href="{{my_services}}" target="_blank">Go To My Services</a><br /> <br/>We are excited to be empowering your business.<br/><br/>
Thank you<br/>
The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //9
            [
                'name'       => 'Business Profile Unpublished',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{platform_name}} Business Profile Unpublished',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/> As requested, your {{platform_name}} Business Profile page
for {{practitioner_business_name}} is now unpublished.
Your Service Listings are also unpublished and you can no longer receive new Client Bookings.
If you have existing Client Bookings, you will need to honour them, unless you choose to cancel them.
You can republish Business Profile at any time by going to your Profile Page and clicking the PUBLISH button. <br/>
<a href="{{my_account}}" target="_blank">Go To My Profile</a><br/><br/>
 Thank you<br/> The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //10
            [
                'name'       => 'Service Schedule Live - WS/Event/Physical',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{service_name}} Booking Schedule is Live!',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
Your Booking Schedule is live for {{service_name}} on {{platform_name}} and is ready for Clients to book. The unique website address for this service is:
<a href="{{service_url}}" target="_blank">{{service_url}}</a>. Make sure to promote it on Social Media to get more bookings!<br />
{{service_name}} - {{schedule_name}}<br />
From: {{schedule_start_date}}, {{schedule_start_time}}<br/>
To: {{schedule_end_date}}, {{schedule_end_time}}<br />
Location: {{schedule_venue}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}<br />
<a href="{{add_to_calendar}}" target="_blank">Add to calendar</a> <br />
We are excited to be empowering your business.<br/><br/>
Thank you<br/> The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //11
            [
                'name'       => 'Service Schedule Live - Event/Virtual',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{service_name}} Booking Schedule is Live!',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/> Your Booking Schedule is live for {{service_name}} on {{platform_name}} and is ready for Clients to book.
The unique website address for this service is: <a href="{{service_url}}" target="_blank">{{service_url}}</a>.
Make sure to promote it on Social Media to get more bookings!<br/>
{{service_name}} - {{schedule_name}}<br />
From: {{schedule_start_date}}, {{schedule_start_time}}<br />
To: {{schedule_end_date}}, {{schedule_end_time}}<br/>
Virtual Location: <a href="{{schedule_hosting_url}}" target="_blank">Virtual Location</a><br/>
<a href="{{add_to_calendar}}" target="_blank">Add to calendar</a>
<br/> We are excited to be empowering your business.<br/><br/>
Thank you <br/>The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //12
            [
                'name'       => 'Service Schedule Live - Retreat',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Retreat Booking Schedule Live for {{service_name}}!',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/> Your Retreat\'s Booking Schedule is live for {{service_name}}
on {{platform_name}} and is ready for Clients to book.
The unique website address for this service is: <a href="{{service_url}}" target="_blank">{{service_url}}</a>.
Make sure to promote it on Social Media to get more bookings!<br/>
{{service_name}} - {{schedule_name}}<br />
From: {{schedule_start_date}}<br />
To: {{schedule_end_date}}<br/>
Location: {{schedule_city}}, {{schedule_country}}<br />
<a href="{{add_to_calendar}}" target="_blank">Add to calendar</a><br/>
We are excited to be empowering your business.<br/><br/>
Thank you <br/>
The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //13
            [
                'name'       => 'Service Schedule Live - Appointments',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{service_name}} Schedule is Live!',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>
Your Booking Schedule {{schedule_name}} is live for {{service_name}} on {{platform_name}} and is ready for Clients to book.
The unique website address for this service is: <a href="{{service_url}}" target="_blank">{{service_url}}</a>.
Make sure to promote it on Social Media to get more bookings!
We are excited to be empowering your business. <br/>
<br/>Thank you <br/>
 The {{platform_name}} Team <br/>',
                'delay'      => random_int(5, 20)
            ],
            //14
            [
                'name'       => 'Service Schedule Live - Date-less',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{service_name}} Ready for Purchase on {{platform_name}}!',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>
Your Schedule {{schedule_name}} is live for {{service_name}} on {{platform_name}} and is ready for Clients to buy.
The unique website address for this service is: <a href="{{service_url}}" target="_blank">{{service_url}}</a>.
Make sure to promote it on Social Media to get more sales!
We are excited to be empowering your business. <br/> <br/> Thank you  <br/>The {{platform_name}} Team <br/>',
                'delay'      => random_int(5, 20)
            ],
            //15
            [
                'name'       => 'Service Unpublished',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{service_name}} Unpublished',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
This is to confirm {{service_name}} is now unpublished on {{platform_name}} and and you can no longer receive new Client Bookings for it.
If you have existing Client Bookings, you will need to honour them, unless you choose to cancel them.
You can republish it at any time by going to your Service Listing and clicking the PUBLISH button.
<br/><br /><a href="{{my_services}}" target="_blank">Go To My Services</a><br /><br/><br/> Thank you<br/> The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //16
            [
                'name'       => 'Service Listing Live',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Service Listing Live on {{platform_name}}!',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
Congratulations! Your Service Listing {{service_name}} is live on {{platform_name}}.
The unique website address for this service is: <a href="{{service_url}}" target="_blank">{{service_url}}</a>
 You can use this to promote your service directly on flyers, social media posts and more!<br/>
The next step in gaining new clients is to add your Service Schedule if you have not yet done so. Here is a guide to help you do this.
<br /><a href="{{my_services}}" target="_blank">Go To My Services</a><br /> We are excited to be empowering your business.<br/><br/>
Thank you<br/>
 The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //17
            [
                'name'       => 'Subscription confirmation - Paid',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{platform_name}} - {{subscription_tier_name}} Subscription',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>Welcome to the {{platform_name}} - {{subscription_tier_name}} Subscription Plan.
We hope you enjoy using the platform. If you need help at any stage, please contact us at {{platform_email}} or visit the FAQs.
Your card will be charged a monthly subscription fee of {{subscription_cost}}, and you may be charged for cancellation fee’s if you cancel a Client booking.
You can change your subscription at any time from your Account section.
<br/><br /><a href="{{my_account}}" target="_blank">Go To My Account</a><br/> We are excited to empower you in your business.
 <br/><br/>Thank you <br/>The {{platform_name}}  Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //18
            [
                'name'       => 'Subscription confirmation - Free',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{platform_name}} -  Free Subscription',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>
Welcome to the {{platform_name}}  Free Subscription Plan. We hope you enjoy using the features available on your plan.
You can also upgrade your subscription at any time from your Account section.
 You will not be charged a monthly subscription fee. Please note, your card may be charged for cancellation fee’s if you cancel a Client booking.
 <br/><br /><a href="{{my_account}}" target="_blank">Go To My Account</a><br/>
 We are excited to empower you in your business. <br/><br/>Thank you <br/>The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //19
            [
                'name'       => 'Change of Subscription',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{platform_name}} Subscription Plan Changed',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>
We are confirming your {{platform_name}} Subscription Plan has now been changed to {{subscription_tier_name}}, effective from {{subscription_start_date}}
<br/><a href="{{my_account}}" target="_blank">Go To My Account</a><br/>
We are excited to empower you in your business. <br/><br/>
Thank you<br/> The {{platform_name}}  Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //20
            [
                'name'       => 'Article Published',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Your Article is Live on {{platform_name}}!',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
 Congratulations! Your Article: {{article_name}} is live on {{platform_name}}  and visible to potential clients.
 The unique website address is for this service is: <a href="{{article_url}}" target="_blank">{{article_url}}</a>.<br/>
 Make sure to share it on your Social Media!<br/> <a href="{{my_articles}}" target="_blank">Go to My Articles</a><br/>
 We are excited to be empowering your business.
 <br/><br/>Thank you<br/> The {{platform_name}}  Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //21
            [
                'name'       => 'Article Unpublished',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{article_name}} Unpublished',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/> This is to confirm {{article_name}} is now unpublished on {{platform_name}}
and no longer viewable. You can republish it at any time by going to your Article Page and clicking the PUBLISH button.
<br/><a href="{{my_articles}}" target="_blank">Go to My Articles</a>
<br/><br/> Thank you
<br/>The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //22
            [
                'name'       => 'Service Schedule Cancelled',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Service Schedule Cancelled on {{platform_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
 {{schedule_name}} has now been cancelled for {{service_name}}.<br/><br/> Thank you<br/> The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //23
            [
                'name'       => 'Booking Cancelled by Practitioner',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Cancelled – {{booking_reference}} - {{practitioner_business_name}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
Unfortunately, {{practitioner_business_name}} has had to cancel your booking for {{service_name}}.You will be refunded fully for any amount you have paid.
Cancelled Service: <br/>
Booking Reference: {{booking_reference}}<br/>
{{service_name}} - {{schedule_name}}
From: {{schedule_start_date}}, {{schedule_start_time}}
To: {{schedule_end_date}}, {{schedule_end_time}}
Cost: {{total_paid}}<br/><br/>
 Thank you<br/> The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //24
            [
                'name'       => 'Booking Cancelled by Client with Refund',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Cancelled – {{booking_reference}} - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>
Your booking for {{service_name}} with {{practitioner_business_name}} has now been cancelled. You do not need to take any further action as we will advise the practitioner.
You will be fully refunded. Please allow up to 10 days for the refund to reach you. <br/> <br/>
Cancelled Service: {{service_name}} - {{schedule_name}}<br/>
Booking Reference: {{booking_reference}} <br/>
Cost: {{total_paid}}<br/><br/>
Thank you<br/>
The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //25
            [
                'name'       => 'Booking Cancelled by Client with Refund',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Cancelled – {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
Unfortunately, {{client_name}} has had to cancel their booking with you for {{service_name}} - {{schedule_name}}.
Their place will be reopened in your service schedule for resale. As per your cancellation terms, they will be refunded
fully for any amount they have paid to date for this service.
Please make sure you have funds are available to cover this refund.<br/><br/> Thank you <br/>The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //26
            [
                'name'       => 'Booking Cancelled by Client NO Refund',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Cancelled – {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/> Your booking for {{service_name}} with {{practitioner_business_name}}
has now been cancelled. You do not need to take any further action as we will advise the practitioner.
Unfortunately, you will not be refunded based on the cancellation terms set by the Practitioner.<br/><br/>
Cancelled Service: {{service_name}} - {{schedule_name}} <br/>
Booking Reference: {{booking_reference}}<br/><br/>
 Thank you <br/> The {{platform_name}} Team <br/>',
                'delay'      => random_int(5, 20)
            ],
            //27
            [
                'name'       => 'Booking Cancelled by Client NO Refund',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Cancelled – {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/> Unfortunately, {{client_name}} has had to cancel their booking with you for {{service_name}} {{schedule_name}}.
Their place will be reopened in your service schedule for resale.
As per your cancellation terms, they will not be refunded for this service. <br/> <br/>
Thank you <br/> The {{platform_name}} Team <br/>',
                'delay'      => random_int(5, 20)
            ],
            //28
            [
                'name'       => 'Booking Confirmation - Event Virtual',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Confirmation - {{booking_reference}} - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}<br/>
Booking Details: {{service_name}} - {{schedule_name}}<br/>
Booking Reference: {{booking_reference}} <br/>
Cost: {{total_paid}} <br/>
From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}} <br/>
Location: {{schedule_hosting_url}} <br/><a href="{{add_to_calendar}}" target="_blank">Add to calendar</a> <br/> <a href="{{view_booking}}" target="_blank">View My Bookings</a>
Your Practitioner may have also added some attachments to this email for you.<br/><br/>
Thank you<br/> The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //29
            [
                'name'       => 'Booking Confirmation - Event Virtual',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Confirmation - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>
Congratulations! {{client_name}} has booked with you for {{service_name}}. <br/>
Booking Details: {{service_name}} - {{schedule_name}}<br/>
Booking Reference: {{booking_reference}} <br/>
Cost: {{total_paid}} <br/>
From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}<br/>
Location: {{schedule_hosting_url}}<br/> {{view_booking}} <br/><br/>
Thank you <br/>The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //30
            [
                'name'       => 'Booking Confirmation - DateLess Virtual',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Order Confirmation - {{booking_reference}} - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
 Your Purchase for {{service_name}} from {{practitioner_business_name}} is confirmed. <br/>
 Purchase Details: {{service_name}} - {{schedule_name}}<br/>
Order Reference: {{booking_reference}} <br/>
Cost: {{total_paid}} <br/>
Location: {{schedule_hosting_url}} <br/>{{view_booking}} <br/>
Message from {{practitioner_business_name}}:
<br/>{{practitioner_schedule_message}}<br/><br/>
Your Practitioner may have also added some attachments to this email for you and should be in touch
with you via {{platform_name}} email message to confirm further details.
 <br/><br/>Thank you <br/>The {{platform_name}}  Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //31
            [
                'name'       => 'Booking Confirmation - Dateless Virtual',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Purchase Confirmation - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>
 Congratulations! {{client_name}} has purchased {{service_name}}. <br/>
 Purchase Details: {{service_name}} - {{schedule_name}}<br/>
Order Reference: {{booking_reference}} <br/>
Cost: {{total_paid}} <br/>
Location: {{schedule_hosting_url}} <br/>{{view_booking}}<br/>
 We recommend getting in touch with {{client_name}} directly via {{platform_name}} email message to welcome them and provide any further information they may need for {{service_name}}.
 <br/><br/>Thank you <br/>The {{platform_name}}  Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //32
            [
                'name'       => 'Booking Confirmation - Date/Apt Physical',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Confirmation - {{booking_reference}} - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>
 Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}} <br/> <br/>
 Booking Details: {{service_name}} - {{schedule_name}} <br/>
 Booking Reference: {{booking_reference}} <br/>
 Cost: {{total_paid}} <br/>
 From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}} <br/>
 Location:  {{schedule_venue}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}} <br/>
<a href="{{add_to_calendar}}" target="_blank">Add to calendar</a>
<br/> {{see_on_map}} <br/>
 Message from {{practitioner_business_name}}:
<br/>{{practitioner_schedule_message}}<br/><br/>
Your Practitioner may have also added some attachments to this email for you. <br/> <br/>
Thank you  <br/> The {{platform_name}} Team <br/>',
                'delay'      => random_int(5, 20)
            ],
            //33
            [
                'name'       => 'Booking Confirmation - Date/Apt Physical',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Confirmation - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/> Congratulations! {{client_name}} has booked with you for {{service_name}}. <br/>
 Booking Details: {{service_name}} - {{schedule_name}} <br/>
 Booking Reference: {{booking_reference}} <br/>
 Cost: {{total_paid}} <br/>
From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}  <br/>
Location:  {{schedule_venue}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}} <br/>
{{view_booking}} <br/> <br/>
 Thank you <br/> The {{platform_name}} Team <br/>',
                'delay'      => random_int(5, 20)
            ],
            //34
            [
                'name'       => 'Booking Confirmation - Dateless Physical',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Purchase Confirmation - {{booking_reference}} - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>
Your Purchase for {{service_name}} from {{practitioner_business_name}} is confirmed. <br/>
Purchase Details: {{service_name}} - {{schedule_name}} <br/>
Booking Reference: {{booking_reference}}  <br/>
Cost: {{total_paid}} <br/>
Location: {{schedule_city}}, {{schedule_country}} <br/>
 Message from {{practitioner_business_name}}:
<br/>{{practitioner_reschedule_message}}<br/><br/>
{{view_booking}} <br/>
Your Practitioner may have also added some attachments to this email for you and should also be in touch with you via {{platform_name}} messaging to confirm further details.
 <br/> <br/>Thank you  <br/>The {{platform_name}} Team <br/>',
                'delay'      => random_int(5, 20)
            ],
            //35
            [
                'name'       => 'Booking Confirmation - Dateless Physical',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Purchase Confirmation - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>
Congratulations! {{client_name}} has purchased {{service_name}}. <br/>
 Purchase Details: {{service_name}} - {{schedule_name}} <br/>
 Booking Reference: {{booking_reference}} <br/>
 Cost: {{total_paid}} <br/>
 Location: {{schedule_city}}, {{schedule_country}} <br/>
We recommend getting in touch with {{client_name}} directly via {{platform_name}} messaging to welcome them and provide any further information they may need for {{service_name}}.
 <br/>{{view_booking}} <br/> <br/>  Thank you <br/> The {{platform_name}} Team <br/> <br/>',
                'delay'      => random_int(5, 20)
            ],
            //36
            [
                'name'       => 'Account Terminated by Admin',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{platform_name}} Account Termination',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
Unfortunately, your {{platform_name}} account has been terminated and your existing bookings with Practitioners or from Clients have been cancelled.<br/>
Reason for Termination: {{admin_termination_message}}<br/>
We are sorry that you will no longer be able to use our platform. If you have any questions, please contact us at {{platform_email}}.<br/><br/>
Thank you <br/>The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //37
            [
                'name'       => 'Account Terminated by Admin',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '{{platform_name}} Account Termination',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
Unfortunately, your {{platform_name}} account has been terminated and your existing bookings with Practitioners have been cancelled.<br/> Reason for Termination:
{{admin_termination_message}}<br/> We are sorry that you will no longer be able to use our platform. If you have any questions, please contact us at {{platform_email}}.
<br/><br/>Thank you <br/>The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //38
            [
                'name'       => 'Service Updated by Practitioner (Non-Contractual)',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'A Service You Have Booked is Updated - {{booking_reference}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/> We thought you may like to know that {{service_name}} which you have booked with {{practitioner_business_name}} has been updated.
This does not change your Booking which is still as listed below, though the changes may include an updated venue/location details or additional information which may be of interest you.
<br/>{{view_the_service}}<br/>
{{view_booking}}<br/><br/>
Your current booking: {{service_name}} - {{schedule_name}}<br/>
Booking Reference: {{booking_reference}}<br/>
From: {{schedule_start_date}}, {{schedule_start_time}}<br/>
To: {{schedule_end_date}}, {{schedule_end_time}}<br/>
Location: {{schedule_hosting_url}}
{{schedule_venue}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}
<br/>{{see_on_map}} <br/>
<br/>Thank you <br/>The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //39
            [
                'name'       => 'Service Updated by Practitioner (Contractual)',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Important! Your Booking Has Been Changed – {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/> You are currently booked with {{practitioner_business_name}} for {{service_name}}.
<br /> Booking Reference: {{booking_reference}}<br />
{{practitioner_business_name}} has had to change the booking as follows: {{service_name}} - {{schedule_name}}<br/>
From: {{schedule_start_date}}, {{schedule_start_time}}<br/>
To: {{schedule_end_date}}, {{schedule_end_time}}<br/>
Location: {{schedule_hosting_url}} {{schedule_venue}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}<br/>
You can either Accept or Decline this change. This will not impact the price you have paid for the service.
If you Decline, your booking will be cancelled and you will be refunded in full.
Please note, if you do not reply, this will be considered as accepting the change.<br/>  {{accept}} <br />{{decline}} <br/>
<a href="{{service_url}}" target="_blank">View the service</a><br/>
<a href="{{view_booking}}" target="_blank">View My Booking</a><br/><br/>
Thank you<br/> The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            /*
             *
             * CRON REMINDERS
             *
             */
            //40
            [
                'name'       => 'Booking Reminder - WS/Event',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '1 Week to Go - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
Your booking for {{service_name}} with {{practitioner_business_name}} is just one week away. <br/>
Booking Details: {{service_name}} - {{schedule_name}} <br/>
Booking Reference: {{booking_reference}} <br/>
From: {{schedule_start_date}}, {{schedule_start_time}}<br/>
To: {{schedule_end_date}}, {{schedule_end_time}}<br/>
Location:  {{schedule_venue}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}
 <br/> {{see_on_map}}<br/>
<a href="{{view_bookings}}" target="_blank">View My Bookings</a><br/><br/>
Thank you<br/> The {{platform_name}} Team <br/>',
                'delay'      => random_int(5, 20)
            ],
            //41
            [
                'name'       => 'Booking Reminder - Retreat',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => '2 Weeks to Go - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>Your Retreat, {{service_name}} with {{practitioner_business_name}} is just two weeks away. <br/>
Booking Details: {{service_name}} - {{schedule_name}}<br/>
Booking Reference: {{booking_reference}} <br/>
From: {{schedule_start_date}}<br/>
To: {{schedule_end_date}} <br/>
Location: {{schedule_city}}, {{schedule_country}}<br/>
<a href="{{view_bookings}}" target="_blank">View My Bookings</a>
<br/><br/> Thank you<br/> The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //42
            [
                'name'       => 'Booking Reminder - WS/Event/Retreat/Appointment',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Tomorrow - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>
Your booking for {{service_name}} with {{practitioner_business_name}} is tomorrow.  <br/>
Booking Details: {{service_name}} - {{schedule_name}} <br/>
Booking Reference: {{booking_reference}} <br/>
From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}} <br/>
Location:  {{schedule_venue}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}}
<br/>{{see_on_map}}<br/>
<a href="{{view_bookings}}" target="_blank">View My Bookings</a><br/><br/> Thank you <br/>The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            /*
             *
             * CRON REMINDERS END
             *
             */
            //43
            [
                'name'       => 'Booking Reschedule Offered by Practitioner - Date',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Reschedule Request – {{booking_reference}} - {{practitioner_business_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/> You are currently booked with {{practitioner_business_name}} for {{service_name}} on the {{schedule_start_date}}.<br/>
Booking Reference: {{booking_reference}} {{practitioner_business_name}} would like to reschedule your booking as follows:<br/>
Reschedule Requested:{{service_name}} - {{schedule_name}}<br/>
From: {{reschedule_start_date}}, {{reschedule_start_time}}<br/>
To: {{reschedule_end_date}}, {{reschedule_end_time}}<br/>
Location: {{reschedule_venue}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}<br/>
Message from {{practitioner_business_name}}:
<br/>{{practitioner_reschedule_message}}<br/><br/>

{{accept}} <br/>{{decline}}<br/>
 <a href="{{view_booking}}" target="_blank">View My Booking</a><br/><br/>
You will not be charged for this reschedule. Please note, if you decline or do not respond, the Practitioner may still cancel your booking and if so, you will be refunded.<br/>
Current Booking: {{service_name}} - {{schedule_name}}<br/>
From: {{schedule_start_date}}, {{schedule_start_time}}<br/>
To: {{schedule_end_date}}, {{schedule_end_time}}<br/>
Location:  {{schedule_hosting_url}} {{schedule_venue}}
{{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}<br/><br/>
Thank you<br/> The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //44
            [
                'name'       => 'Booking Reschedule Offered by Practitioner - Appt',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Reschedule Request – {{practitioner_business_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>
 You are currently booked with {{practitioner_business_name}} for {{service_name}}, on the {{schedule_start_date}}. <br/>
Booking Reference: {{booking_reference}} {{practitioner_business_name}} would like to reschedule your booking as follows: <br/>
New Appointment: {{service_name}} - {{schedule_name}} <br/>
From: {{reschedule_start_date}}, {{reschedule_start_time}} To: {{reschedule_end_date}}, {{reschedule_end_time}} <br/>
Location: {{reschedule_venue}} {{reschedule_hosting_url}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}} <br/> <br/>
You can either Accept or Decline this request. If you decline or do not respond, the Practitioner may still cancel your current appointment and if so, you will be refunded.
 <br/>
 Message from {{practitioner_business_name}}:
<br/>{{practitioner_reschedule_message}}<br/><br/>
 {{accept}} {{decline}} <br/>
 <a href="{{view_booking}}" target="_blank">View My Bookings</a> <br/> <br/>
Current Booking {{service_name}} - {{schedule_name}} <br/>
From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}} <br/>
Location:  {{schedule_hosting_url}} {{schedule_venue}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}} <br/> <br/>
Thank you <br/> The {{platform_name}} Team <br/>',
                'delay'      => random_int(5, 20)
            ],
            //45
            [
                'name'       => 'Client Rescheduled FYI',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Rescheduled - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}} <br/>
 Your Client, {{client_name}} has rescheduled their booking for {{service_name}}. They are now booked in for:
{{service_name}} - {{schedule_name}}  <br/>
From: {{reschedule_start_date}}, {{reschedule_start_time}} To: {{reschedule_end_date}}, {{reschedule_end_time}} <br/>
Location: {{reschedule_venue}} {{reschedule_hosting_url}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}} <br/>
 Their original booking will be reopened in your service schedule for resale. <br/> <br/>
 Original Booking: {{service_name}} - {{schedule_name}} <br/>
 From: {{schedule_start_date}}, {{schedule_start_time}}<br/>
 To: {{schedule_end_date}}, {{schedule_end_time}} <br/>
Location:  {{schedule_hosting_url}} {{schedule_venue}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}} <br/> <br/>
Thank you <br/> The {{platform_name}} Team <br/>',
                'delay'      => random_int(5, 20)
            ],
            //46
            [
                'name'       => 'Booking Reschedule Accepted by Client',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Change Confirmation - {{booking_reference}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
 We are pleased to confirm your booking with {{practitioner_business_name}} for {{service_name}} has now been changed.<br/><br/>
Booking Reference: {{booking_reference}} {{service_name}} - {{schedule_name}}<br/>
From: {{schedule_start_date}}, {{schedule_start_time}}<br/>
To: {{schedule_end_date}}, {{schedule_end_time}}<br/>
Location: {{schedule_venue}} {{schedule_hosting_url}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}<br/>
{{see_on_map}}<br/>
 <a href="{{add_to_calendar}}" target="_blank">Add to calendar</a>
 <a href="{{view_booking}}" target="_blank">View My Bookings</a><br/><br/>
  Thank you<br/> The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //47
            [
                'name'       => 'Booking Reschedule Accepted by Client',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Change Confirmation - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/> We are pleased to confirm your Client {{client_name}} has accepted the change for {{service_name}}.<br/><br/>
Booking Reference: {{booking_reference}} They are now booked in for: {{service_name}} - {{schedule_name}}<br/>
From: {{schedule_start_date}}, {{schedule_start_time}}<br/>
To: {{schedule_end_date}}, {{schedule_end_time}}<br/>
Location: {{schedule_venue}} {{schedule_hosting_url}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}<br/>
Thank you<br/> The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //48
            [
                'name'       => 'Reschedule Request Declined by Client',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Reschedule Declined - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/> Unfortunately, your Client {{client_name}} has declined the reschedule for {{service_name}}.
Their original booking will remain. If you are not able to deliver the booking, you can still cancel it and the client will be refunded.<br/><br/>
Booking Reference: {{booking_reference}}<br/>
Current Booking - MAINTAINED {{service_name}} - {{schedule_name}}<br/>
From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}<br/>
Location:  {{schedule_hosting_url}} {{schedule_venue}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}
<br/>{{view_booking}}<br/><br/>
Reschedule - DECLINED {{service_name}} - {{schedule_name}}<br/>
From: {{reschedule_start_date}}, {{reschedule_start_time}} To: {{reschedule_end_date}}, {{reschedule_end_time}}<br/>
Location: {{reschedule_venue}} {{reschedule_hosting_url}}
{{reschedule_address}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}<br/><br/>
Thank you<br/> The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],
            //49
            [
                'name'       => 'Contractual Service Update Declined - Booking Cancelled',
                'user_type'  => 'client',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Cancelled - {{booking_reference}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/> Unfortunately, {{practitioner_business_name}} had to change the booking details for {{service_name}} - {{schedule_name}}.
As you declined the change, your booking has been cancelled. You will be refunded for this booking. Please allow up to 10 days for the refund to reach you.<br/>
Cancelled Service:{{service_name}} - {{schedule_name}} <br/>
Booking Reference: {{booking_reference}} <br/>
Cost: {{total_paid}} <br/> <br/>
Thank you <br/> The {{platform_name}} Team <br/>',
                'delay'      => random_int(5, 20)
            ],
            //50
            [
                'name'       => 'Reschedule Request No Reply from Client',
                'user_type'  => 'practitioner',
                'from_email' => Str::random(10) . '@gmail.com',
                'from_title' => Str::random(8),
                'subject'    => 'Booking Reschedule Not Confirmed - {{service_name}}',
                'logo'       => Str::random(5),
                'text'       => 'Hi {{first_name}}<br/>
Unfortunately, your Client {{client_name}} has not responded to the request to reschedule for {{service_name}}.
Their original booking will remain. If you are not able to deliver the booking, you can still cancel it and the client will be refunded.<br/><br/>
Booking Reference: {{booking_reference}}<br/>
Current Booking - MAINTAINED {{service_name}} - {{schedule_name}}<br/>
From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}<br/>
Location:  {{schedule_hosting_url}} {{schedule_venue}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}
<br/>{{view_booking}}<br/><br/>
Reschedule - NO RESPONSE {{service_name}} - {{schedule_name}}<br/>
From: {{reschedule_start_date}}, {{reschedule_start_time}} To: {{reschedule_end_date}}, {{reschedule_end_time}}<br/>
Location:{{reschedule_venue}} {{reschedule_hosting_url}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}<br/>
<br/> Thank you<br/> The {{platform_name}} Team<br/>',
                'delay'      => random_int(5, 20)
            ],


            /**
            //46
            [
            'name'       => 'Booking Reschedule Client to Select - Appt',
            'user_type'  => 'client',
            'from_email' => Str::random(10) . '@gmail.com',
            'from_title' => Str::random(8),
            'subject'    => 'Booking Reschedule Request – {{practitioner_business_name}}',
            'logo'       => Str::random(5),
            'text'       => 'Hi {{first_name}} <br/> {{practitioner_business_name}} will not be able to make your exiting appointment for {{service_name}}.<br/>
            Instead they would like to offer you another appointment at a time of your choosing.
            You can either Accept this request and book a new time or Decline this request. If you decline or do not respond,
            the Practitioner may still cancel your current appointment and if so, you will be refunded.
            <br/>{{accept_rebook}} {{decline}} <br/>
            <a href="{{view_booking}}" target="_blank">View My Bookings</a> <br/>
            Message from {{practitioner_business_name}} <br/> {{practitioner_reschedule_message}} <br/> <br/>
            Your current booking: {{service_name}} - {{schedule_name}} <br/>
            Booking Reference: {{booking_reference}} <br/>
            From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}} <br/>
            Location:  {{schedule_hosting_url}} {{schedule_venue}}
            {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}} <br/> <br/>
            Thank you The {{platform_name}} Team',
            'delay'      => random_int(5, 20)
            ],
             * [
             * 'name'       => 'Booking Confirmation - DateLess Physical - with Deposit',
             * 'user_type'  => 'client',
             * 'from_email' => Str::random(10) . '@gmail.com',
             * 'from_title' => Str::random(8),
             * 'subject'    => 'Purchase Confirmation - {{service_name}}',
             * 'logo'       => Str::random(5),
             * 'text'       => 'Hi {{first_name}} <br/>
             * Congratulations! {{client_name}} has purchased {{service_name}}. <br/><br/>
             * Purchase Details: {{service_name}} - {{schedule_name}}<br/>
             * Booking Reference: {{booking_reference}} <br/>
             * Location: {{schedule_city}}, {{schedule_country}} {{view_booking}} <br/>
             * The Client has paid a deposit of {{deposit_paid]} and will pay the remaining over instalments as follows:<br/>
             * {{instalment_date_1}} – {{instalment_amount_1}}<br/>
             * {{instalment_date_2}} – {{instalment_amount_2}}<br/>
             * {{etc_for_number_of_instalments}}<br/><br/>
             * Thank you <br/>The {{platform_name}} Team<br/>',
             * 'delay'      => random_int(5, 20)
             * ],
             *             //60
             * [
             * 'name'       => 'Booking Confirmation Dateless Virtual – with Deposit',
             * 'user_type'  => 'practitioner',
             * 'from_email' => Str::random(10) . '@gmail.com',
             * 'from_title' => Str::random(8),
             * 'subject'    => 'Purchase Confirmation - {{service_name}}',
             * 'logo'       => Str::random(5),
             * 'text'       => 'Hi {{first_name}} <br/>
             * Congratulations! {{client_name}} has purchased {{service_name}}.<br/>
             * Purchase Details: {{service_name}} - {{schedule_name}}<br/>
             * Order Reference: {{booking_reference}} <br/>
             * Location: {{schedule_hosting_url}} <br/>{{view_booking}}<br/>
             * The Client has paid a deposit of {{deposit_paid]} and will pay the remaining over instalments as follows:<br/>
             * {{instalment_date_1}} – {{instalment_amount_1}}<br/>
             * {{instalment_date_2}} – {{instalment_amount_2}}<br/>
             * {{etc_for_number_of_instalments}}<br/><br/> Thank you<br/> The {{platform_name}}  Team<br/>',
             * 'delay'      => random_int(5, 20)
             * ],
             * //21
             * [
             * 'name'       => 'Instalment Payment Reminder',
             * 'user_type'  => 'client',
             * 'from_email' => Str::random(10) . '@gmail.com',
             * 'from_title' => Str::random(8),
             * 'subject'    => 'Payment Reminder {{booking_reference}} - {{service_name}}',
             * 'logo'       => Str::random(5),
             * 'text'       => 'Hi {{first_name}} <br/>
             * This is to remind you that your next instalment payment for {{service_name}} from {{practitioner_business_name}} is due in 7 days.
             * The Instalment Payment Schedule is charged to your card provided as follows:
             * <br/>{{instalments}}<br />
             * Service: {{service_name}} - {{schedule_name}} <br/>
             * Booking Reference: {{booking_reference}} <br/>
             * <a href="{{view_booking}}" target="_blank">View My Booking</a><br/><br/>
             * Thank you<br/>
             * The {{platform_name}} Team<br/>',
             * 'delay'      => random_int(5, 20)
             * ],
             * //59
             * [
             * 'name'       => 'Booking Event Virtual - with Deposit',
             * 'user_type'  => 'practitioner',
             * 'from_email' => Str::random(10) . '@gmail.com',
             * 'from_title' => Str::random(8),
             * 'subject'    => 'Booking Confirmation - {{service_name}}',
             * 'logo'       => Str::random(5),
             * 'text'       => 'Hi {{first_name}} <br/>Congratulations! {{client_name}} has booked with you for {{service_name}}. <br/>
             * Booking Details: {{service_name}} - {{schedule_name}}<br/>
             * Booking Reference: {{booking_reference}} <br/>
             * From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}<br/>
             * Location: {{schedule_hosting_url}}<br/> {{view_booking}}<br/>
             * The Client has paid a deposit of {{deposit_paid]} and will pay the remaining over instalments as follows:<br/>
             * {{instalment_date_1}} – {{instalment_amount_1}}<br/>
             * {{instalment_date_2}} – {{instalment_amount_2}}<br/>
             * {{etc_for_number_of_instalments}}<br/><br/> Thank you <br/>The {{platform_name}}  Team<br/>',
             * 'delay'      => random_int(5, 20)
             * ],
             * //56
             * [
             * 'name'       => 'Booking Confirmation - DateLess Virtual with Deposit',
             * 'user_type'  => 'client',
             * 'from_email' => Str::random(10) . '@gmail.com',
             * 'from_title' => Str::random(8),
             * 'subject'    => 'Order Confirmation - {{booking_reference}} - {{service_name}}',
             * 'logo'       => Str::random(5),
             * 'text'       => 'Hi {{first_name}} <br/>
             * Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}  <br/>
             * Purchase Details: {{service_name}} - {{schedule_name}}<br/>
             * Order Reference: {{booking_reference}} <br/>
             * Location: {{schedule_hosting_url}} <br/>{{view_booking}} <br/>
             * Message from {{practitioner_business_name}}
             * <br/>{{practitioner_booking_message}} <br/>
             * Your Practitioner may have also added some attachments to this email for you and should also be in touch with you via {{platform_name}} email message to confirm further details.
             * <br/>Payment Deposit Paid: {{deposit_paid}} <br/>
             * The balance for this service will be charged to your card proved as follows:<br/>
             * {{instalment_date_1}} – {{instalment_amount_1}}<br/>
             * {{instalment_date_2}} – {{instalment_amount_2}}<br/>
             * {{etc_for_number_of_instalments}} <br/>Please make sure you have funds available for each instalment or your purchase may be cancelled. <br/><br/>
             * Thank you <br/>The {{platform_name}}  Team<br/>',
             * 'delay'      => random_int(5, 20)
             * ],
             * //53
             * [
             * 'name'       => 'Booking Confirmation - Event Virtual With Deposit',
             * 'user_type'  => 'client',
             * 'from_email' => Str::random(10) . '@gmail.com',
             * 'from_title' => Str::random(8),
             * 'subject'    => 'Booking Confirmation - {{booking_reference}} - {{service_name}}',
             * 'logo'       => Str::random(5),
             * 'text'       => 'Hi {{first_name}}<br/>
             * Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}. <br/>
             * Booking Details: {{service_name}} - {{schedule_name}} <br/>
             * Booking Reference: {{booking_reference}} <br/>
             * From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}} <br/>
             * Location: {{schedule_hosting_url}} <br/><a href="{{add_to_calendar}}" target="_blank">Add to calendar</a> <br/> <a href="{{view_booking}}" target="_blank">View My Bookings</a> <br/>
             * Message from {{practitioner_business_name}} <br/>{{practitioner_booking_message}} <br/>
             * Your Practitioner may have also added some attachments to this email for you. Payment Deposit Paid: {{deposit_paid}} The balance for this service will be charged to your card proved as follows:<br/>
             * {{instalment_date_1}} – {{instalment_amount_1}}<br/>
             * {{instalment_date_2}} – {{instalment_amount_2}}<br/>
             * {{etc_for_number_of_instalments}} <br/>
             * Please make sure you have funds available for each instalment or your Booking may be cancelled. <br/><br/>Thank you<br/> The {{platform_name}}  Team<br/>',
             * 'delay'      => random_int(5, 20)
             * ],
             * //40
             * [
             * 'name'       => 'Booking Confirmation - Date Physical - with Deposit',
             * 'user_type'  => 'client',
             * 'from_email' => Str::random(10) . '@gmail.com',
             * 'from_title' => Str::random(8),
             * 'subject'    => 'Booking Confirmation - {{booking_reference}} - {{service_name}}',
             * 'logo'       => Str::random(5),
             * 'text'       => 'Hi {{first_name}}<br/> Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}<br/>
             * Booking Details: {{service_name}} - {{schedule_name}}<br/>
             * Booking Reference: {{booking_reference}} <br/>
             * From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}}<br/>
             * Location:  {{schedule_venue}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}}
             * <br/><a href="{{add_to_calendar}}" target="_blank">Add to calendar</a>{{see_on_map}}<br/>
             * Message from {{practitioner_business_name}}<br/>
             * {{practitioner_booking_message}} <br/>
             * Your Practitioner may have also added some attachments to this email for you. <br/>Payment Deposit Paid: {{deposit_paid]} <br/>
             * The balance for this service will be charged to your card provided as follows:<br/>
             * {{installments}}<br/>
             * Please make sure you have funds available for each instalment or your Booking may be cancelled. <br/><br/>
             * Thank you <br/>The {{platform_name}} Team<br/>',
             * 'delay'      => random_int(5, 20)
             * ],
             * //41
             * [
             * 'name'       => 'Booking Confirmation - Date Physical - with Deposit',
             * 'user_type'  => 'practitioner',
             * 'from_email' => Str::random(10) . '@gmail.com',
             * 'from_title' => Str::random(8),
             * 'subject'    => 'Booking Confirmation - {{service_name}}',
             * 'logo'       => Str::random(5),
             * 'text'       => 'Hi {{first_name}} <br/>
             * Congratulations! {{client_name}}  has booked with you for {{service_name}}. <br/>
             * Booking Details: {{service_name}} - {{schedule_name}}<br/>
             * Booking Reference: {{booking_reference}} <br/>
             * From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}} <br/>
             * Location:  {{schedule_venue}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}}<br/>
             * {{view_booking}} <br/>
             * The Client has paid a deposit of {{deposit_paid}} and will pay the remaining over instalments as follows:<br/>
             * {{instalment_date_1}} – {{instalment_amount_1}}<br/>
             * {{instalment_date_2}} – {{instalment_amount_2}}<br/>
             * {{etc_for_number_of_instalments}}<br/><br/>
             * Thank you <br/>The {{platform_name}} Team<br/>',
             * 'delay'      => random_int(5, 20)
             * ],
             * //42
             * [
             * 'name'       => 'Booking Confirmation - DateLess Physical - with Deposit',
             * 'user_type'  => 'practitioner',
             * 'from_email' => Str::random(10) . '@gmail.com',
             * 'from_title' => Str::random(8),
             * 'subject'    => 'Purchase Confirmation - {{booking_reference}} - {{service_name}}',
             * 'logo'       => Str::random(5),
             * 'text'       => 'Hi {{first_name}}<br/>
             * Your purchase for {{service_name}} is now confirmed with {{practitioner_business_name}}<br/><br/>
             * Purchase Details: {{service_name}} - {{schedule_name}} <br/>
             * Booking Reference: {{booking_reference}}<br/>
             * Location: {{schedule_city}}, {{schedule_country}} <br/>{{view_booking}} <br/>
             * Message from {{practitioner_business_name}}<br/>
             * {{practitioner_booking_message}} <br/>
             * Your Practitioner may have also added some attachments to this email for you.<br/>
             * Payment Deposit Paid: {{deposit_paid}} <br/>
             * The balance for this service will be charged to your card provided as follows:<br/>
             * {{instalment_date_1}} – {{instalment_amount_1}}<br/>
             * {{instalment_date_2}} – {{instalment_amount_2}}<br/>
             * {{etc_for_number_of_instalments}}<br/>
             * Please make sure you have funds available for each instalment or your purchase may be cancelled.<br/><br/>
             * Thank you <br/>The {{platform_name}} Team<br/>',
             * 'delay'      => random_int(5, 20)
             * ],
             **/

        ];
        DB::table('custom_emails')->insert($data);
    }
}
