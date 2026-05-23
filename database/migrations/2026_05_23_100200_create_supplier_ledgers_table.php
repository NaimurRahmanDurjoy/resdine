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
        Schema::create('supplier_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('reference_type')->nullable(); // 'purchase', 'payment', 'opening_balance'
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('debit_amount', 15, 2)->default(0); // Payments
            $table->decimal('credit_amount', 15, 2)->default(0); // Invoices
            $table->decimal('balance', 15, 2)->default(0); // Running balance
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_ledgers');
    }
};
