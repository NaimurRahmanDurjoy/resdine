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
        Schema::create('acc_fiscal_years', function (Blueprint $table) {

            $table->id();

            // Fiscal Year Name
            $table->string('name')->unique();

            // Start Date
            $table->date('start_date');

            // End Date
            $table->date('end_date');

            // Current Active Year
            $table->boolean('is_active')
                ->default(false);

            // Closed or Open
            $table->boolean('is_closed')
                ->default(false);

            $table->text('description')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('is_active');
            $table->index('is_closed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_fiscal_years');
    }
};
