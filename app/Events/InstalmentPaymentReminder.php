<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\Instalment;
use App\Models\Purchase;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class InstalmentPaymentReminder
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Purchase $purchase;
    public Schedule $schedule;
    public User $user;
    public User $client;
    public User $practitioner;
    public Service $service;
    public Booking $booking;
    public Collection $installments;
    public Instalment $installmentNext;

    public function __construct(Purchase $purchase, Instalment $installment)
    {
        $this->purchase = $purchase;
        $this->schedule = $purchase->schedule;
        $this->service = $purchase->service;
        $this->installments = collect($installment);
        $this->installmentNext = $installment;
        $this->user = $this->client = $purchase->user;
        $this->booking = $purchase->bookings()->first();
        $this->practitioner = $purchase->service->user;
    }
}
