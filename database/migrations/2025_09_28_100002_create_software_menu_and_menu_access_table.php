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
        Schema::create('software_menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');              // e.g. "Dashboard", "Users"
            $table->string('route')->nullable(); // Laravel route name or URL
            $table->string('icon')->nullable();  // e.g. "fa fa-users"
            $table->unsignedBigInteger('parent_id')->nullable(); // for submenus
            $table->integer('order')->default(0); // sorting
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('software_menus')->onDelete('cascade');
        });

        Schema::create('software_menu_access', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('menu_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('menu_id')->references('id')->on('software_menus')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_menus');
        Schema::dropIfExists('software_menu_access');
    }
};
