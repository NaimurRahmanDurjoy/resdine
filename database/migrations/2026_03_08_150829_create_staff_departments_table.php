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
        Schema::create('staff_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('branch_id');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });

        // Adding section to restaurant_tables if not exists
        if (Schema::hasTable('restaurant_tables')) {
            Schema::table('restaurant_tables', function (Blueprint $table) {
                if (!Schema::hasColumn('restaurant_tables', 'section')) {
                    $table->string('section')->nullable()->after('name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_departments');
    }
};
