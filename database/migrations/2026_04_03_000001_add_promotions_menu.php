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
        $parent = DB::table('software_menus')->where('name', 'Customers & Loyalty')->first();
        
        if ($parent) {
            DB::table('software_menus')->insert([
                'name' => 'Promotions',
                'route' => 'admin.promotions.index',
                'parent_id' => $parent->id,
                'icon' => 'campaign',
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('software_menus')->where('name', 'Promotions')->delete();
    }
};
