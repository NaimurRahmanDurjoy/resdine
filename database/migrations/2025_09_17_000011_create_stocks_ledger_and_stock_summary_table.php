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
        Schema::create('stock_ledger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ingredient_id');
            $table->unsignedBigInteger('branch_id');

            // Transaction type stored as INT
            $table->unsignedTinyInteger('transaction_type')->comment('1=purchase, 2=sale, 3=return_in, 4=return_out, 5=adjustment_in, 6=adjustment_out, 7=transfer_in, 8=transfer_out');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('invoice_id, purchase_id, adjustment_id etc.');
            $table->string('reference_type', 50)->nullable()->comment('purchase, invoice, adjustment');

            // Stock movement
            $table->decimal('qty_in', 12, 2)->default(0);
            $table->decimal('qty_out', 12, 2)->default(0);
            $table->decimal('unit_cost', 12, 2)->default(0);

            // Batch & expiry tracking
            $table->string('batch_no', 100)->nullable()->comment('Batch/Lot number if applicable');
            $table->date('expiry_date')->nullable()->comment('Expiry date for perishable stock');
            $table->dateTime('transaction_date')->useCurrent();

            $table->timestamps();

            // Indexes
            $table->index('ingredient_id');
            $table->index('branch_id');
            $table->index('transaction_type');
            $table->index('transaction_date');
            $table->index('batch_no');
            $table->index('expiry_date');
        });


        Schema::create('stock_summary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ingredient_id');
            $table->unsignedBigInteger('branch_id');
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->decimal('average_cost', 12, 2)->default(0);
            // Batch & expiry
            $table->string('batch_no', 100)->nullable()->comment('Batch/Lot number if applicable');
            $table->date('expiry_date')->nullable()->comment('Expiry date for perishable stock');
            $table->dateTime('last_transaction_date')->nullable();

            $table->timestamps();
            // Unique ensures one row per product+branch+batch
            $table->unique(['ingredient_id', 'branch_id', 'batch_no']);
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_ledger');
        Schema::dropIfExists('stock_summary');
    }
};
