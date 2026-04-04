<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_master_id');
            $table->tinyInteger('method')->comment('1=cash,2=card,3=mobile banking,4=wallet');
            $table->decimal('amount', 10, 2);
            $table->string('transaction_id')->nullable()->comment('for card/mobile payments');
            $table->string('payment_reference')->nullable()->comment('for additional info like card type, mobile operator, etc.');
            $table->tinyInteger('status')->default(1)->comment('1=completed,2=failed,3=refunded');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('order_master_id')->references('id')->on('order_masters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
