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
        Schema::create('acc_voucher_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained('acc_vouchers')->cascadeOnDelete();
            
            // source_type: 1=POS Payment, 2=Purchase Bill, 3=Staff Salary, 4=Expense
            $table->tinyInteger('source_type'); 
            $table->unsignedBigInteger('source_id');
            
            $table->json('metadata')->nullable(); // For any extra context
            $table->timestamps();

            $table->index(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_voucher_sources');
    }
};
