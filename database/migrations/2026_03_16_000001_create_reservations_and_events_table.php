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
        Schema::create('reservations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('table_id');
            $table->unsignedBigInteger('branch_id');
            $table->dateTime('reservation_time');
            $table->integer('guests_count');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('special_requests')->nullable();
            $table->tinyInteger('status')->default(1); // 1=confirmed, 0=cancelled, 2=arrived
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('table_id')->references('id')->on('restaurant_tables')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });

        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->unsignedBigInteger('branch_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('estimated_guests')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('admin_approval')->default(0); // 0=pending, 1=approved, 2=rejected
            $table->tinyInteger('status')->default(1); // 1=active, 0=inactive
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('events');
    }
};
