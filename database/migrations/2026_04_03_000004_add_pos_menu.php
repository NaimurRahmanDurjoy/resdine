<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Try to find a logical parent for POS. Let's put it on top of Operations or create "Sales"
        $topLevelId = DB::table('software_menus')->insertGetId([
            'name' => 'Point of Sale',
            'route' => 'admin.pos.index',
            'icon' => 'point_of_sale',
            'order' => 5, // Top priority menu
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('software_menus')->where('name', 'Point of Sale')->delete();
    }
};
