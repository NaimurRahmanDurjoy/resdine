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
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('image')->nullable();
            $table->tinyInteger('is_available')->default(1)->comment('1=available,0=not available');
            $table->tinyInteger('type')->default(1)->comment('1=regular,2=combo,3=complementary');
            $table->timestamps();
        });

        Schema::create('combo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_id')->constrained('menu_items'); // must be type=2
            $table->foreignId('item_id')->constrained('menu_items');
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('complementary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_item_id')->constrained('menu_items');
            $table->foreignId('item_id')->constrained('menu_items');
            $table->tinyInteger('is_mandatory')->default(0)->comment('1=mandatory,0=optional');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('combo_items');
        Schema::dropIfExists('complementary_items');
    }
};
