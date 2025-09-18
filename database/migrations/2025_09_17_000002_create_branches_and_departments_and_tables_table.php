<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('branches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('location')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('branch_id');
            $table->integer('capacity')->default(4);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });

        Schema::create('res_departments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('branch_id');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
        Schema::dropIfExists('restaurant_tables');
        Schema::dropIfExists('res_departments');
    }
};
