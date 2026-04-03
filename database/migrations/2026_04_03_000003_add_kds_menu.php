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
        // Try to find a logical parent for KDS. Maybe "Orders" or "Operations".
        // Let's create an "Operations" parent if it doesn't exist, or just put it in a prominent position.
        
        $operationsId = DB::table('software_menus')->insertGetId([
            'name' => 'Operations',
            'icon' => 'settings_applications',
            'order' => 15, // High up
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('software_menus')->insert([
            'name' => 'Kitchen Display (KDS)',
            'route' => 'admin.kds.index',
            'parent_id' => $operationsId,
            'icon' => 'restaurant',
            'order' => 1,
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
        DB::table('software_menus')->where('name', 'Kitchen Display (KDS)')->delete();
        DB::table('software_menus')->where('name', 'Operations')->delete();
    }
};
