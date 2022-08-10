<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds columns to refund payments of instalments and their transfers.
 */
class AddInvoiceToInstalmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instalments', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('payment_date');
            $table->unsignedBigInteger('transfer_id')->nullable()->after('subscription_id');
            $table->string('stripe_invoice_id')->nullable()->after('transfer_id');
            $table->string('stripe_payment_id')->nullable()->after('stripe_invoice_id')->comment('Payment indent ID');
            $table->string('stripe_charge_id')->nullable()->after('stripe_payment_id');
            $table->string('stripe_refund_id')->nullable()->after('stripe_charge_id');
            $table->timestamp('refunded_at')->nullable()->after('stripe_refund_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instalments', function (Blueprint $table) {
            $table->dropColumn('refunded_at');
            $table->dropColumn('stripe_refund_id');
            $table->dropColumn('stripe_charge_id');
            $table->dropColumn('stripe_payment_id');
            $table->dropColumn('stripe_invoice_id');
            $table->dropColumn('transfer_id');
            $table->dropColumn('paid_at');
        });
    }
}
