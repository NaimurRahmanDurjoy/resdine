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
        Schema::create('software_menu_access', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('menu_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->integer('created_by_type')->nullable(); // 1=admin,2=staff
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->integer('updated_by_type')->nullable(); // 1=admin,2=staff
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->integer('deleted_by_type')->nullable(); // 1=admin,2=staff


            $table->foreign('menu_id')->references('id')->on('software_menus')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_menu_access');
    }
};
