<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */

    public function run()
    {
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Thank you for creating your Client Account on {{platform_name}}. We are extremely excited to welcome you and empower you on your personal transformation journey. To begin, please verify your email by clicking on the button below or copying and pasting the long URL into your browser:</p></td></tr><tr><td><p class="slate-p"><a href="{{email_verification_url}}" class="slate-link">Verify Email</a></p><p class="slate-p"></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Thank you for creating your Practitioner Account on {{platform_name}}. We are extremely excited to welcome you and to empower you in your Business.</p></td></tr><tr></tr><tr><td><p class="slate-p">You will be able to advertise your business and sell your services and book the services of other practitioners. Here is a guide to help you get started.</p></td></tr><tr><td><p class="slate-p">To begin, please verify your email by clicking on the button below or copying and pasting the long URL into your browser:</p></td></tr><tr><td><p class="slate-p"><a href="{{email_verification_url}}" class="slate-link">Verify Email</a></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">A request has been received to change the password for your {{platform_name}} account.</p></td></tr><tr><td><p class="slate-p"><a href="{{reset_password_url}}" target="_blank">Reset Password</a></p></td></tr><tr><td><p class="slate-p">If you did not initiate this request, please contact us immediately at {{platform_email}}</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">This is to confirm the password for your {{platform_name}} account has been changed. If you did not initiate this change, please contact us immediately at {{platform_email}}.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">This is to confirm your {{platform_name}} account has now been closed. If you did not initiate this change, please contact us immediately at {{platform_email}}.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">This is to confirm your {{platform_name}} Practitioner account has now been deleted. If you did not initiate this change, please contact us immediately at {{platform_email}}.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Thank you for upgrading to a Practitioner Account on {{platform_name}}.</p></td></tr><tr><td><p class="slate-p">We are extremely excited to empower you in your Business. You will be able to advertise your business and sell your services and still book services of other practitioners. Here is a guide to help you get started.</p></td></tr><tr><td><p class="slate-p">Your Subscription is {{subscription_tier_name}}. You can change your subscription plan at any time from your Account section.</p></td></tr><tr><td><p class="slate-p"><a href="{{my_account}}" target="_blank">Go To My Account</a></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Congratulations! Your business profile page is live on {{platform_name}} and visible to potential clients. Your website address is: <a href="{{practitioner_url}}" target="_blank">Your business address</a>. You can add this to your business card, flyers, social media profiles and more! The next step in gaining new clients is to advertise your services. Here is a guide to help you get started.</p></td></tr><tr><td><p class="slate-p"><a href="{{my_services}}" target="_blank">Go To My Services</a></p></td></tr><tr><td><p class="slate-p">We are excited to be empowering your business.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">As requested, your {{platform_name}} Business Profile page for {{practitioner_business_name}} is now unpublished. Your Service Listings are also unpublished and you can no longer receive new Client Bookings. If you have existing Client Bookings, you will need to honour them, unless you choose to cancel them. You can republish Business Profile at any time by going to your Profile Page and clicking the PUBLISH button.</p></td></tr><tr><td><p class="slate-p"><a href="{{my_account}}" target="_blank">Go To My Profile</a></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Your Booking Schedule is live for {{service_name}} on {{platform_name}} and is ready for Clients to book. The unique website address for this service is: <a href="{{service_url}}" target="_blank">{{service_url}}</a>. Make sure to promote it on Social Media to get more bookings!</p></td></tr><tr><td><p class="slate-p">{{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p"><a href="{{add_to_calendar}}" target="_blank">Add to calendar</a></p></td></tr><tr><td><p class="slate-p">We are excited to be empowering your business.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Your Booking Schedule is live for {{service_name}} on {{platform_name}} and is ready for Clients to book. The unique website address for this service is: <a href="{{service_url}}" target="_blank">{{service_url}}</a>. Make sure to promote it on Social Media to get more bookings!</p></td></tr><tr><td><p class="slate-p">{{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Virtual Location: <a href="{{schedule_hosting_url}}" target="_blank">Virtual Location</a></p></td></tr><tr><td><p class="slate-p"><a href="{{add_to_calendar}}" target="_blank">Add to calendar</a></p></td></tr><tr><td><p class="slate-p">We are excited to be empowering your business.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Your Retreat`s Booking Schedule is live for {{service_name}} on {{platform_name}} and is ready for Clients to book. The unique website address for this service is: <a href="{{service_url}}" target="_blank">{{service_url}}</a>. Make sure to promote it on Social Media to get more bookings!</p></td></tr><tr><td><p class="slate-p">{{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_city}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p"><a href="{{add_to_calendar}}" target="_blank">Add to calendar</a></p></td></tr><tr><td><p class="slate-p">We are excited to be empowering your business.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Your Booking Schedule {{schedule_name}} is live for {{service_name}} on {{platform_name}} and is ready for Clients to book. The unique website address for this service is: <a href="{{service_url}}" target="_blank">{{service_url}}</a>. Make sure to promote it on Social Media to get more bookings!</p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">We are excited to be empowering your business.</p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Your Schedule {{schedule_name}} is live for {{service_name}} on {{platform_name}} and is ready for Clients to buy.The unique website address for this service is: <a href="{{service_url}}" class="slate-link">{{service_url}}</a> .Make sure to promote it on Social Media to get more sales!We are excited to be empowering your business.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">This is to confirm {{service_name}} is now unpublished on {{platform_name}} and and you can no longer receive new Client Bookings for it. If you have existing Client Bookings, you will need to honour them, unless you choose to cancel them.You can republish it at any time by going to your Service Listing and clicking the PUBLISH button.</p></td></tr><tr><td><p class="slate-p"><a href="{{my_services}}" class="slate-link">Go To My Services</a></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Congratulations! Your Service Listing {{service_name}} is live on {{platform_name}}. The unique website address for this service is: <a href="{{service_url}}" target="_blank">{{service_url}}</a> You can use this to promote your service directly on flyers, social media posts and more! The next step in gaining new clients is to add your Service Schedule if you have not yet done so. Here is a guide to help you do this.</p></td></tr><tr><td><p class="slate-p"><a href="{{my_services}}" class="slate-link">Go To My Services</a></p></td></tr><tr><td><p class="slate-p">We are excited to be empowering your business.</p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Welcome to the {{platform_name}} - {{subscription_tier_name}} Subscription Plan. We hope you enjoy using the platform. If you need help at any stage, please contact us at {{platform_email}} or visit the FAQs. Your card will be charged a monthly subscription fee of {{subscription_cost}}, and you may be charged for cancellation fee’s if you cancel a Client booking. You can change your subscription at any time from your Account section.</p></td></tr><tr><td><p class="slate-p"><a href="{{my_account}}" target="_blank">Go To My Account</a></p></td></tr><tr><td><p class="slate-p">We are excited to be empowering your business.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Welcome to the {{platform_name}} Free Subscription Plan. We hope you enjoy using the features available on your plan. You can also upgrade your subscription at any time from your Account section. You will not be charged a monthly subscription fee. Please note, your card may be charged for cancellation fee’s if you cancel a Client booking.</p></td></tr><tr><td><p class="slate-p"><a href="{{my_account}}" target="_blank">Go To My Account</a></p></td></tr><tr><td><p class="slate-p">We are excited to be empowering your business.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">We are confirming your {{platform_name}} Subscription Plan has now been changed to {{subscription_tier_name}}, effective from {{subscription_start_date}}</p></td></tr><tr><td><p class="slate-p"><a href="{{my_account}}" target="_blank">Go To My Account</a></p></td></tr><tr><td><p class="slate-p">We are excited to be empowering your business.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Congratulations! Your Article: {{article_name}} is live on {{platform_name}} and visible to potential clients. The unique website address is for this service is: <a href="{{article_url}}" target="_blank">{{article_url}}</a>.</p></td></tr><tr><td><p class="slate-p">Make sure to share it on your Social Media! <a href="{{my_articles}}" target="_blank">Go to My Articles</a></p></td></tr><tr><td><p class="slate-p"><a href="{{my_articles}}" target="_blank">Go to My Articles</a></p></td></tr><tr><td><p class="slate-p">We are excited to be empowering your business.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">This is to confirm {{article_name}} is now unpublished on {{platform_name}} and no longer viewable. You can republish it at any time by going to your Article Page and clicking the PUBLISH button.</p></td></tr><tr><td><p class="slate-p"><a href="{{my_articles}}" target="_blank">Go to My Articles</a></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">{{schedule_name}} has now been cancelled for {{service_name}}.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Unfortunately, {{practitioner_business_name}} has had to cancel your booking for {{service_name}}.You will be refunded fully for any amount you have paid.</p></td></tr><tr><td><p class="slate-p">Cancelled Service:</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">{{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Your booking for {{service_name}} with {{practitioner_business_name}} has now been cancelled. You do not need to take any further action as we will advise the practitioner. You will be fully refunded. Please allow up to 10 days for the refund to reach you.</p></td></tr><tr><td><p class="slate-p">Cancelled Service: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Unfortunately, {{client_name}} has had to cancel their booking with you for {{service_name}} - {{schedule_name}}. Their place will be reopened in your service schedule for resale. As per your cancellation terms, they will be refunded fully for any amount they have paid to date for this service. Please make sure you have funds are available to cover this refund.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Your booking for {{service_name}} with {{practitioner_business_name}} has now been cancelled. You do not need to take any further action as we will advise the practitioner. Unfortunately, you will not be refunded based on the cancellation terms set by the Practitioner.</p></td></tr><tr><td><p class="slate-p">Cancelled Service: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Unfortunately, {{client_name}} has had to cancel their booking with you for {{service_name}} {{schedule_name}}. Their place will be reopened in your service schedule for resale. As per your cancellation terms, they will not be refunded for this service.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}</p></td></tr><tr><td><p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}}</p></td></tr><tr><td><p class="slate-p"><a href="{{add_to_calendar}}" target="_blank">Add to calendar</a></p></td></tr><tr><td><p class="slate-p"><a href="{{view_booking}}" target="_blank">View My Bookings</a></p></td></tr><tr><td><p class="slate-p">Your Practitioner may have also added some attachments to your booking.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Congratulations! {{client_name}} has booked with you for {{service_name}}.</p></td></tr><tr><td><p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}}</p></td></tr><tr><td><p class="slate-p">{{view_booking}}</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Your Purchase for {{service_name}} from {{practitioner_business_name}} is confirmed.</p></td></tr><tr><td><p class="slate-p">Purchase Details: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Order Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}}</p></td></tr><tr><td><p class="slate-p">{{view_booking}}</p></td></tr><tr><td><p class="slate-p">Message from {{practitioner_business_name}}:</p></td></tr><tr><td><p class="slate-p">{{practitioner_schedule_message}}</p></td></tr><tr><td><p class="slate-p">Your Practitioner may have also added some attachments to this email for you and should be in touchwith you via {{platform_name}} email message to confirm further details.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Congratulations! {{client_name}} has purchased {{service_name}}.</p></td></tr><tr><td><p class="slate-p">Purchase Details: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Order Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}}</p></td></tr><tr><td><p class="slate-p">{{view_booking}}</p></td></tr><tr><td><p class="slate-p">We recommend getting in touch with {{client_name}} directly via {{platform_name}} email message to welcome them and provide any further information they may need for {{service_name}}.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Your booking for {{service_name}} is now confirmed with {{practitioner_business_name}}</p></td></tr><tr><td><p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}}</p></td></tr><tr><td><p class="slate-p"><a href="{{add_to_calendar}}" target="_blank">Add to calendar</a></p></td></tr><tr><td><p class="slate-p">{{see_on_map}}</p></td></tr><tr><td><p class="slate-p">Message from {{practitioner_business_name}}:</p></td></tr><tr><td><p class="slate-p">{{practitioner_schedule_message}}</p></td></tr><tr><td><p class="slate-p">Your Practitioner may have also added some attachments to this email for you.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Congratulations! {{client_name}} has booked with you for {{service_name}}.</p></td></tr><tr><td><p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}}</p></td></tr><tr><td><p class="slate-p">{{view_booking}}</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Your Purchase for {{service_name}} from {{practitioner_business_name}} is confirmed.</p></td></tr><tr><td><p class="slate-p">Purchase Details: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}} {{schedule_country}}</p></td></tr><tr><td><p class="slate-p">Message from {{practitioner_business_name}}:</p></td></tr><tr><td><p class="slate-p">{{practitioner_schedule_message}}</p></td></tr><tr><td><p class="slate-p">{{view_booking}}</p></td></tr><tr><td><p class="slate-p">Your Practitioner may have also added some attachments to this email for you and should also be in touch with you via {{platform_name}} messaging to confirm further details.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Congratulations! {{client_name}} has purchased {{service_name}}.</p></td></tr><tr><td><p class="slate-p">Purchase Details: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">Cost: {{total_paid}}</p></td></tr><td><p class="slate-p">Location: {{schedule_city}}, {{schedule_country}}</p></td><td><p class="slate-p">We recommend getting in touch with {{client_name}} directly via {{platform_name}} messaging to welcome them and provide any further information they may need for {{service_name}}.</p></td><td><p class="slate-p">{{view_booking}}</p></td><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Unfortunately, your {{platform_name}} account has been terminated and your existing bookings with Practitioners or from Clients have been cancelled.</p></td></tr><tr><td><p class="slate-p">Reason for Termination: {{admin_termination_message}}</p></td></tr><tr><td><p class="slate-p">We are sorry that you will no longer be able to use our platform. If you have any questions, please contact us at {{platform_email}}.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Unfortunately, your {{platform_name}} account has been terminated and your existing bookings with Practitioners have been cancelled.</p></td></tr><tr><td><p class="slate-p">Reason for Termination: {{admin_termination_message}}</p></td></tr><tr><td><p class="slate-p">We are sorry that you will no longer be able to use our platform. If you have any questions, please contact us at {{platform_email}}.</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">We thought you may like to know that {{service_name}} which you have booked with {{practitioner_business_name}} has been updated.This does not change your Booking which is still as listed below, though the changes may include an updated venue/location details or additional information which may be of interest you.</p></td></tr><tr><td><p class="slate-p">{{view_the_service}}</p></td></tr><tr><td><p class="slate-p">{{view_booking}}</p></td></tr><tr><td><p class="slate-p">Your current booking: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}} {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p">{{see_on_map}}</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">You are currently booked with {{practitioner_business_name}} for {{service_name}}.</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">{{practitioner_business_name}} has had to change the booking as follows:</p></td></tr><tr><td><p class="slate-p">{{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}} {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p">You can either Accept or Decline this change. This will not impact the price you have paid for the service.If you Decline, your booking will be cancelled and you will be refunded in full.Please note, if you do not reply, this will be considered as accepting the change.</p></td></tr><tr><td><p class="slate-p">{{accept}}</p></td></tr><tr><td><p class="slate-p">{{decline}}</p></td></tr><tr><td><p class="slate-p"><a href="{{service_url}}" target="_blank">View the service</a></p></td></tr><tr><td><p class="slate-p"><a href="{{view_booking}}" target="_blank">View My Booking</a></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Your booking for {{service_name}} with {{practitioner_business_name}} is just one week away.</p></td></tr><tr><td><p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}} {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p">{{see_on_map}}</p></td></tr><tr><td><p class="slate-p"><a href="{{view_bookings}}" target="_blank">View My Bookings</a></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Your Retreat, {{service_name}} with {{practitioner_business_name}} is just two weeks away.</p></td></tr><tr><td><p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_city}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p"><a href="{{view_bookings}}" target="_blank">View My Bookings</a></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Your booking for {{service_name}} with {{practitioner_business_name}} is tomorrow.</p></td></tr><tr><td><p class="slate-p">Booking Details: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}} {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p">{{see_on_map}}</p></td></tr><tr><td><p class="slate-p"><a href="{{view_bookings}}" target="_blank">View My Bookings</a></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">You are currently booked with {{practitioner_business_name}} for {{service_name}} on the {{schedule_start_date}}.</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">{{practitioner_business_name}} would like to reschedule your booking as follows:</p></td></tr><tr><td><p class="slate-p">Reschedule Requested:{{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{reschedule_start_date}}, {{reschedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{reschedule_end_date}}, {{reschedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{reschedule_venue_name}} {{reschedule_venue_address}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}</p></td></tr><tr><td><p class="slate-p">Message from {{practitioner_business_name}}:</p></td></tr><tr><td><p class="slate-p">{{practitioner_reschedule_message}}</p></td></tr><tr><td><p class="slate-p"><a href="{{accept}}" target="_blank" rel="noreferrer" data-button-link="true" style="text-decoration:none"><span style="padding:10px 24px;margin-bottom:8px;color:#fff;text-align:center;background-color:#db7a6a;line-height:45px;border-radius:25px;cursor:pointer;user-select:none" contenteditable="false">Accept</span></a></p></td></tr><tr><td><p class="slate-p"><a href="{{decline}}" target="_blank" rel="noreferrer" data-button-link="true" style="text-decoration:none"><span style="padding:10px 24px;margin-bottom:8px;color:#fff;text-align:center;background-color:#db7a6a;line-height:45px;border-radius:25px;cursor:pointer;user-select:none" contenteditable="false">Decline</span></a></p></td></tr><tr><td><p class="slate-p"><a href="{{view_booking}}" target="_blank">View My Booking</a></p></td></tr><tr><td><p class="slate-p">You will not be charged for this reschedule.</p></td></tr><tr><td><p class="slate-p">Please note, if you decline or do not respond, the Practitioner may still cancel your booking and if so, you will be refunded.</p></td></tr><tr><td><p class="slate-p">Current Booking: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}} {{schedule_venue_name}} {{schedule_venue_address}}{{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">You are currently booked with {{practitioner_business_name}} for {{service_name}}, on the {{schedule_start_date}}.</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">{{practitioner_business_name}} would like to reschedule your booking as follows:</p></td></tr><tr><td><p class="slate-p">New Appointment: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{reschedule_start_date}}, {{reschedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{reschedule_end_date}}, {{reschedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{reschedule_venue_name}} {{reschedule_venue_address}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}</p></td></tr><tr><td><p class="slate-p">You can either Accept or Decline this request. If you decline or do not respond, the Practitioner may still cancel your current appointment and if so, you will be refunded.</p></td></tr><tr><td><p class="slate-p">Message from {{practitioner_business_name}}:</p></td></tr><tr><td><p class="slate-p">{{practitioner_reschedule_message}}</p></td></tr><tr><td><p class="slate-p"><a href="{{accept}}" target="_blank" rel="noreferrer" data-button-link="true" style="text-decoration:none"><span style="padding:10px 24px;margin-bottom:8px;color:#fff;text-align:center;background-color:#db7a6a;line-height:45px;border-radius:25px;cursor:pointer;user-select:none" contenteditable="false">Accept</span></a></p></td></tr><tr><td><p class="slate-p"><a href="{{decline}}" target="_blank" rel="noreferrer" data-button-link="true" style="text-decoration:none"><span style="padding:10px 24px;margin-bottom:8px;color:#fff;text-align:center;background-color:#db7a6a;line-height:45px;border-radius:25px;cursor:pointer;user-select:none" contenteditable="false">Decline</span></a></p></td></tr><tr><td><p class="slate-p"><a href="{{view_booking}}" target="_blank">View My Booking</a></p></td></tr><tr><td><p class="slate-p">Current Booking: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}} {{schedule_venue_name}} {{schedule_venue_address}}{{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Your Client, {{client_name}} has rescheduled their booking for {{service_name}}.</p></td></tr><tr><td><p class="slate-p">They are now booked in for: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{reschedule_start_date}}, {{reschedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{reschedule_end_date}}, {{reschedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{reschedule_venue_name}} {{reschedule_venue_address}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}</p></td></tr><tr><td><p class="slate-p">Their original booking will be reopened in your service schedule for resale.</p></td></tr><tr><td><p class="slate-p">Original Booking: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}} {{schedule_venue_name}} {{schedule_venue_address}}{{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">We are pleased to confirm your booking with {{practitioner_business_name}} for {{service_name}} has now been changed.</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">{{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_hosting_url}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p">{{see_on_map}}</p></td></tr><tr><td><p class="slate-p"><a href="{{add_to_calendar}}" target="_blank">Add to calendar</a></p></td></tr><tr><td><p class="slate-p"><a href="{{view_booking}}" target="_blank">View My Bookings</a></p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">We are pleased to confirm your Client {{client_name}} has accepted the change for {{service_name}}.</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">They are now booked in for: {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_hosting_url}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Unfortunately, your Client {{client_name}} has declined the reschedule for {{service_name}}.Their original booking will remain. If you are not able to deliver the booking, you can still cancel it and the client will be refunded.</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">Current Booking - MAINTAINED {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}} {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p">{{view_booking}}</p></td></tr><tr><td><p class="slate-p">Reschedule - DECLINED {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{reschedule_start_date}}, {{reschedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{reschedule_end_date}}, {{reschedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{reschedule_venue_name}} {{reschedule_hosting_url}}{{reschedule_address}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Unfortunately, {{practitioner_business_name}} had to change the booking details for {{service_name}} - {{schedule_name}}.As you declined the change, your booking has been cancelled. You will be refunded for this booking. Please allow up to 10 days for the refund to reach you.</p></td></tr><tr><td><p class="slate-p">Cancelled Service:{{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">Cost: {{total_paid}}</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
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
                'text'       => '<tr><td><p class="slate-p">Hi <span data-slate-template="true">{{first_name}}</span></p></td></tr><tr><td><p class="slate-p"></td></tr><tr><td><p class="slate-p">Unfortunately, your Client {{client_name}} has not responded to the request to reschedule for {{service_name}}.Their original booking will remain. If you are not able to deliver the booking, you can still cancel it and the client will be refunded.</p></td></tr><tr><td><p class="slate-p">Booking Reference: {{booking_reference}}</p></td></tr><tr><td><p class="slate-p">Current Booking - MAINTAINED {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{schedule_start_date}}, {{schedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{schedule_end_date}}, {{schedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location: {{schedule_hosting_url}} {{schedule_venue_name}} {{schedule_venue_address}} {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}}</p></td></tr><tr><td><p class="slate-p">{{view_booking}}</p></td></tr><tr><td><p class="slate-p">Reschedule - NO RESPONSE {{service_name}} - {{schedule_name}}</p></td></tr><tr><td><p class="slate-p">From: {{reschedule_start_date}}, {{reschedule_start_time}}</p></td></tr><tr><td><p class="slate-p">To: {{reschedule_end_date}}, {{reschedule_end_time}}</p></td></tr><tr><td><p class="slate-p">Location:{{reschedule_venue_name}} {{reschedule_venue_address}} {{reschedule_hosting_url}} {{reschedule_city}}, {{reschedule_postcode}}, {{reschedule_country}}</p></td></tr><tr><td><p class="slate-p">Thank you</p></td></tr><tr><td><p class="slate-p">The <span data-slate-template="true">{{platform_name}}</span> Team</p></td></tr>',
                'delay'      => random_int(5, 20)
            ],
            /**
             * //46
             * [
             * 'name'       => 'Booking Reschedule Client to Select - Appt',
             * 'user_type'  => 'client',
             * 'from_email' => Str::random(10) . '@gmail.com',
             * 'from_title' => Str::random(8),
             * 'subject'    => 'Booking Reschedule Request – {{practitioner_business_name}}',
             * 'logo'       => Str::random(5),
             * 'text'       => '<tr><td>Hi {{first_name}} <br/> {{practitioner_business_name}} will not be able to make your exiting appointment for {{service_name}}.<br/>
             * Instead they would like to offer you another appointment at a time of your choosing.
             * You can either Accept this request and book a new time or Decline this request. If you decline or do not respond,
             * the Practitioner may still cancel your current appointment and if so, you will be refunded.
             * <br/>{{accept_rebook}} {{decline}} <br/>
             * <a href="{{view_booking}}" target="_blank">View My Bookings</a> <br/>
             * Message from {{practitioner_business_name}} <br/> {{practitioner_reschedule_message}} <br/> <br/>
             * Your current booking: {{service_name}} - {{schedule_name}} <br/>
             * Booking Reference: {{booking_reference}} <br/>
             * From: {{schedule_start_date}}, {{schedule_start_time}} To: {{schedule_end_date}}, {{schedule_end_time}} <br/>
             * Location:  {{schedule_hosting_url}} {{schedule_venue_name}}
             * {{schedule_city}}, {{schedule_postcode}}, {{schedule_country}} <br/> <br/>
             * Thank you The {{platform_name}} Team',
             * 'delay'      => random_int(5, 20)
             * ],
             **/
        ];
        DB::table('custom_emails')->insert($data);
    }
}
