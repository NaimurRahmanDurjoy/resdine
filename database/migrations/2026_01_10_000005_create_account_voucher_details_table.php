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
        Schema::create('acc_voucher_details', function (Blueprint $table) {

            $table->id();

            // Voucher Reference
            $table->foreignId('voucher_id')->constrained('acc_vouchers')->cascadeOnDelete();

            // Account Reference
            $table->foreignId('chart_of_account_id')->constrained('acc_chart_of_accounts')->cascadeOnDelete();

            // Debit Amount
            $table->decimal('debit', 15, 2)->default(0);

            // Credit Amount
            $table->decimal('credit', 15, 2)->default(0);

            // Optional Line Narration
            $table->text('narration')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('voucher_id');
            $table->index('chart_of_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_voucher_details');
    }
};
