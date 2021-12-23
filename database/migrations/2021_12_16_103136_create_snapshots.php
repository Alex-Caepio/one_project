<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Purchase;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\PromotionCode;
use App\Models\Promotion;
use App\Models\Location;
use App\Models\Price;
use App\Models\Booking;
use App\Models\BookingSnapshot;
use App\Models\PurchaseSnapshot;

class CreateSnapshots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE TABLE booking_snapshots LIKE bookings;');
        DB::statement('CREATE TABLE purchase_snapshots LIKE purchases;');
        DB::statement('CREATE TABLE schedule_snapshots LIKE schedules;');
        DB::statement('CREATE TABLE location_snapshots LIKE locations;');
        DB::statement('CREATE TABLE service_snapshots LIKE services;');
        DB::statement('CREATE TABLE promotion_code_snapshots LIKE promotion_codes;');
        DB::statement('CREATE TABLE price_snapshots LIKE prices;');
        DB::statement('CREATE TABLE promotion_snapshots LIKE promotions;');

        Schema::table('purchase_snapshots', function(Blueprint $table){
            $table->foreignIdFor(Purchase::class);
            $table->renameColumn('schedule_id', 'schedule_snapshot_id');
            $table->renameColumn('price_id', 'price_snapshot_id');
            $table->renameColumn('service_id', 'service_snapshot_id');
            $table->renameColumn('promocode_id', 'promocode_snapshot_id');
        });

        Schema::table('schedule_snapshots', function(Blueprint $table){
            $table->foreignIdFor(Schedule::class);
            $table->renameColumn('service_id', 'service_snapshot_id');
            $table->renameColumn('location_id', 'location_snapshot_id');
        });

        Schema::table('location_snapshots', function(Blueprint $table){
            $table->foreignIdFor(Location::class);
            $table->renameColumn('schedule_id', 'schedule_snapshot_id');
        });

        Schema::table('service_snapshots', function(Blueprint $table){
            $table->foreignIdFor(Service::class);
        });

        Schema::table('promotion_code_snapshots', function(Blueprint $table){
            $table->foreignIdFor(PromotionCode::class);
            $table->renameColumn('promotion_id', 'promotion_snapshot_id');
        });

        Schema::table('promotion_snapshots', function(Blueprint $table){
            $table->foreignIdFor(Promotion::class);
        });

        Schema::table('price_snapshots', function(Blueprint $table){
            $table->foreignIdFor(Price::class);
            $table->renameColumn('schedule_id', 'schedule_snapshot_id');
        });

        Schema::table('booking_snapshots', function(Blueprint $table){
            $table->foreignIdFor(Booking::class);
            $table->renameColumn('schedule_id', 'schedule_snapshot_id');
            $table->renameColumn('price_id', 'price_snapshot_id');
            $table->renameColumn('promocode_id', 'promocode_snapshot_id');
            $table->renameColumn('purchase_id', 'purchase_snapshot_id');
        });

        Schema::table('purchases', function(Blueprint $table){
            $table->foreignIdFor(PurchaseSnapshot::class);
        });

        Schema::table('bookings', function(Blueprint $table){
            $table->foreignIdFor(BookingSnapshot::class);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_snapshots');
        Schema::dropIfExists('schedule_snapshots');
        Schema::dropIfExists('location_snapshots');
        Schema::dropIfExists('service_snapshots');
        Schema::dropIfExists('promotion_code_snapshots');
        Schema::dropIfExists('promotion_snapshots');
        Schema::dropIfExists('price_snapshots');
        Schema::dropIfExists('booking_snapshots');
        Schema::dropColumns('purchases', ['purchase_snapshot_id']);
        Schema::dropColumns('bookings', ['booking_snapshot_id']);
    }
}
