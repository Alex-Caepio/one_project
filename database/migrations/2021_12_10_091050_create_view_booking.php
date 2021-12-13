<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateViewBooking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW view_booking AS
                        (select users.id,
                        bookings.practitioner_id,
                         bookings.id as booking_id,
                         bookings.status,
                         bookings.datetime_from,
                         bookings.created_at,
                        services.service_type_id
                        from users
         inner join bookings on bookings.user_id = users.id
         inner join schedules on schedules.id = bookings.schedule_id
         inner join services on services.id = schedules.service_id
        where users.deleted_at is null)"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW view_booking");
    }
}
