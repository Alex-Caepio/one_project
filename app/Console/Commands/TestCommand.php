<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test SMTP credentials';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $email = $this->argument("email");
        Mail::send('base', [], function ($message) use ($email) {
            $message
                ->to($email)
                ->subject('Testing email '.date("Y-m-d H:i:s"));
        });
    }
}
