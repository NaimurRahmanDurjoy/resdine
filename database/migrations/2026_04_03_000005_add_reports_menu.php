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
        $topLevelId = DB::table('software_menus')->insertGetId([
            'name' => 'Reports & Analytics',
            'route' => 'admin.reports.index',
            'icon' => 'query_stats',
            'order' => 8,
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
        DB::table('software_menus')->where('name', 'Reports & Analytics')->delete();
    }
};
