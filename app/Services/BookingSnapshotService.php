<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingSnapshot;
use App\Models\PriceSnapshot;
use App\Models\PromotionSnapshot;
use App\Models\PromotionCodeSnapshot;
use App\Models\PurchaseSnapshot;
use App\Models\ServiceSnapshot;
use App\Models\ScheduleSnapshot;
use App\Models\LocationSnapshot;
use Illuminate\Support\Arr;

class BookingSnapshotService
{
    public static function create(Booking $booking)
    {
        $purchase = $booking->purchase;
        $serviceSnapshotArr = Arr::except($purchase->service->getAttributes(), 'id');
        $serviceSnapshotArr['service_id'] = $purchase->service->id;
        $serviceSnapshot = ServiceSnapshot::create($serviceSnapshotArr);

        $scheduleSnapshotArr = Arr::except($purchase->schedule->getAttributes(), 'id');
        $scheduleSnapshotArr['service_snapshot_id'] = $serviceSnapshot->id;
        $scheduleSnapshotArr['schedule_id'] = $purchase->schedule->id;
        if (!empty($scheduleSnapshotArr['location'])) {
            $locationSnapshotArr = Arr::except($purchase->schedule->location, 'id');
            $locationSnapshotArr['location_id'] = $purchase->schedule->location->id;
            $locationSnapshot = LocationSnapshot::create($locationSnapshotArr);
            $scheduleSnapshotArr['location_snapshot_id'] = $locationSnapshot->id;
        }
        $scheduleSnapshot = ScheduleSnapshot::create($scheduleSnapshotArr);

        $price = $purchase->price()->get()->first();
        $priceSnapshotArr = Arr::except($price->getAttributes(), 'id');
        $priceSnapshotArr['schedule_schedule_id'] = $scheduleSnapshot->id;
        $priceSnapshotArr['price_id'] = $price->id;
        $priceSnapshot = PriceSnapshot::create($priceSnapshotArr);


        if (!empty($purchase->promocode)) {
            $promotionCodeSnapshotArr = Arr::except($purchase->promocode->getAttributes(), 'id');
            if (!empty($promotionCodeSnapshotArr['promotion_id'])) {
                $promotionSnapshotArr = Arr::except($purchase->promocode->promotion->getAttributes(), 'id');
                $promotionSnapshotArr['promotion_id'] = $purchase->promocode->promotion->id;
                $promotionSnapshot = PromotionSnapshot::firstOrCreate(['name' => $promotionSnapshotArr['name']], $promotionSnapshotArr);
            }
            unset($promotionCodeSnapshotArr['promotion_id']);
            $promotionCodeSnapshotArr['promotion_snapshot_id'] = $promotionSnapshot->id;
            $promotionCodeSnapshotArr['promotion_code_id'] = $purchase->promocode->id;
            $promotionCodeSnapshot = PromotionCodeSnapshot::create($promotionCodeSnapshotArr);
        }

        $purchaseSnapshotArr = Arr::except($purchase->getAttributes(), 'id');
        $purchaseSnapshotArr['price_snapshot_id'] = $priceSnapshot->id;
        $purchaseSnapshotArr['schedule_snapshot_id'] = $scheduleSnapshot->id;
        $purchaseSnapshotArr['promocode_snapshot_id'] = $promotionCodeSnapshot->id ?? null;
        $purchaseSnapshotArr['service_snapshot_id'] = $serviceSnapshot->id;
        $purchaseSnapshotArr['purchase_id'] = $purchase->id;
        $purchaseSnapshot = PurchaseSnapshot::create($purchaseSnapshotArr);
        $purchase->purchase_snapshot_id = $purchaseSnapshot->id;
        $purchase->save();

        $bookingSnapshotArr = Arr::except($booking->getAttributes(), 'id');
        $bookingSnapshotArr['schedule_snapshot_id'] = $scheduleSnapshot->id;
        $bookingSnapshotArr['price_snapshot_id'] = $priceSnapshot->id;
        $bookingSnapshotArr['promocode_snapshot_id'] = $promotionCodeSnapshot->id ?? null;
        $bookingSnapshotArr['purchase_snapshot_id'] = $purchaseSnapshot->id;
        $bookingSnapshotArr['booking_id'] = $booking->id;
        $bookingSnapshot = BookingSnapshot::create($bookingSnapshotArr);

        $booking->booking_snapshot_id = $bookingSnapshot->id;
        $booking->save();
    }
}
