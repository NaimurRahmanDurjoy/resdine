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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->tinyInteger('status')->default(1); // 1=active
            $table->timestamps();
        });

        Schema::create('memberships', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name'); // Silver, Gold, Platinum
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->integer('loyalty_multiplier')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('customer_memberships', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('membership_id');
            $table->string('card_no');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('membership_id')->references('id')->on('memberships')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
        Schema::dropIfExists('memberships');
        Schema::dropIfExists('customer_memberships');
    }
};
