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
        Schema::create('acc_voucher_types', function (Blueprint $table) {

            $table->id();

            // Voucher Type Name
            $table->string('name')->unique();

            // Short Prefix
            $table->string('prefix')->nullable();

            // Auto Serial Number Start
            $table->bigInteger('starting_number')
                ->default(1);

            // Active / Inactive
            $table->boolean('status')
                ->default(true);

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_voucher_types');
    }
};
