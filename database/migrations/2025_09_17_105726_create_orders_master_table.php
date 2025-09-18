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
        Schema::create('orders_master', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('user_id')->nullable()->constrained('users'); // created by staff
            $table->foreignId('member_id')->nullable()->constrained('memberships'); // optional customer membership
            $table->unsignedBigInteger('table_id')->nullable()->comment('null if takeaway/delivery');
            $table->tinyInteger('order_type')->default(1)->comment('1=dine-in,2=takeaway,3=delivery,4=party/corporate');
            $table->tinyInteger('status')->default(0)->comment('0=pending,1=preparing,2=ready,3=out_for_delivery,4=completed,5=cancelled');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2)->default(0)->comment('for partial payments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_master');
    }
};
