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
        Schema::create('res_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->timestamps();
        });

        Schema::create('staff_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('users');
            $table->foreignId('department_id')->constrained('departments');
            $table->timestamps();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('res_departments');
        Schema::dropIfExists('staff_departments');
    }
};
