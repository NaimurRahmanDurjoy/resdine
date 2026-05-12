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
        Schema::create('acc_vouchers', function (Blueprint $table) {

            $table->id();

            // Voucher Number
            $table->string('voucher_no')->unique();

            // Voucher Type
            $table->foreignId('voucher_type_id')->constrained('acc_voucher_types')->cascadeOnDelete();

            // Fiscal Year
            $table->foreignId('fiscal_year_id')->constrained('acc_fiscal_years')->cascadeOnDelete();

            // Voucher Date
            $table->date('voucher_date');

            // External Reference
            $table->string('reference_no')->nullable();
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);

            // Notes
            $table->text('narration')->nullable();

            // Draft / Approved / Cancelled
            $table->tinyInteger('status')->default(1)->comment('1=draft, 2=approved, 3=cancelled');

            // User Tracking
            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->foreignId('approved_by')->nullable()->constrained('users');

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('voucher_date');
            $table->index('voucher_type_id');
            $table->index('fiscal_year_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_vouchers');
    }
};
