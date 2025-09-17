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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('unit', 50)->comment('kg, liter, pcs, etc.');
            $table->decimal('min_stock', 10, 2)->default(0);
            $table->boolean('has_expiry')->default(false)->comment('1=expiry item,0=non-expiry');
            $table->integer('expiry_days')->nullable()->comment('shelf life in days');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
