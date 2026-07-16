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
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->integer('created_by_type')->nullable(); // 1=admin,2=staff
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->integer('updated_by_type')->nullable(); // 1=admin,2=staff
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->integer('deleted_by_type')->nullable(); // 1=admin,2=staff

            $table->foreign('parent_id')->references('id')->on('software_menus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_menus');
    }
};
