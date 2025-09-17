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
        Schema::create('menu_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('menu_items');
            $table->string('name'); // e.g., Small, Medium, Large
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_variants');
    }
};
