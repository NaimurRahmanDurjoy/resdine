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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('image')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=available,0=not available');
            $table->tinyInteger('type')->default(1)->comment('1=regular,2=combo,3=complementary');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('menu_categories')->onDelete('set null');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('res_departments')->onDelete('set null');
        });

        Schema::create('combo_item_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('combo_id'); // item id where type=2
            $table->unsignedBigInteger('item_id');  // menu or complementary item
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->foreign('combo_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('menu_items')->onDelete('cascade');
        });

        Schema::create('menu_variants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id'); 
            $table->string('name'); // e.g., Small, Medium, Large
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('menu_items')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('combo_item_details');
        Schema::dropIfExists('menu_variants');
    }
};
