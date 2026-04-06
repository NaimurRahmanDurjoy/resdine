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
        Schema::create('software_menu_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_menu_id')->constrained('software_menus')->cascadeOnDelete();
            $table->string('action'); // view, create, edit, delete
            $table->string('route');  // orders.index, orders.create
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->unique(['software_menu_id', 'action']);
            $table->index('software_menu_id');
            $table->index('route');
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('software_menu_action_id');
            $table->boolean('is_allowed'); // 1 = allow, 0 = deny
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();  

            $table->unique(['role_id', 'software_menu_action_id']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('software_menu_action_id')->references('id')->on('software_menu_actions')->onDelete('cascade');
        });

        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('software_menu_action_id');
            $table->boolean('is_allowed'); // 1 = allow, 0 = deny
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable(); 

            $table->unique(['user_id', 'software_menu_action_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('software_menu_action_id')->references('id')->on('software_menu_actions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('software_menu_actions');
    }
};
