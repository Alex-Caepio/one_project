<?php

namespace App\Events;

use App\Models\Instalment;
use App\Models\Purchase;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class InstalmentPaymentReminder {

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Purchase $purchase;
    public Schedule $schedule;
    public User $user;
    public User $practitioner;
    public Service $service;
    public ?Collection $installments;

    public function __construct(Instalment $instalment, ?Collection $installments) {
        $this->purchase = $instalment->purchase;
        $this->schedule = $instalment->purchase->schedule;
        $this->service = $instalment->purchase->service;
        $this->installments = $installments;
        $this->user = $instalment->purchase->user;
        $this->practitioner = $instalment->purchase->service->user;
    }
}
