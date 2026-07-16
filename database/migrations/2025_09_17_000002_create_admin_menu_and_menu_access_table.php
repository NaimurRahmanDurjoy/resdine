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

        Schema::create('admin_menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('route')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->integer('created_by_type')->nullable(); // 1=admin,2=staff
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->integer('updated_by_type')->nullable(); // 1=admin,2=staff
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->integer('deleted_by_type')->nullable(); // 1=admin,2=staff

            $table->foreign('parent_id')->references('id')->on('admin_menus')->onDelete('cascade');
        });

        Schema::create('admin_menu_access', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('menu_id');
            $table->unsignedBigInteger('admin_id');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->integer('created_by_type')->nullable(); // 1=admin,2=staff
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->integer('updated_by_type')->nullable(); // 1=admin,2=staff
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->integer('deleted_by_type')->nullable(); // 1=admin,2=staff

            $table->foreign('menu_id')->references('id')->on('admin_menus')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_menu_access');
        Schema::dropIfExists('admin_menus');
    }
};
