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
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('min_stock', 10, 2)->default(0);
            $table->boolean('has_expiry')->default(false)->comment('1=expiry item,0=non-expiry');
            $table->integer('expiry_days')->nullable()->comment('shelf life in days');
            $table->timestamps();

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
        });

        Schema::create('recipes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('ingredient_id');
            $table->decimal('quantity', 10, 2);
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->timestamps();

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            $table->foreign('ingredient_id')->references('id')->on('ingrediants')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('menu_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('recipes');
    }
};
