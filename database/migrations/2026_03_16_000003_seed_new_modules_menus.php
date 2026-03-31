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
        // Add Customers & Memberships
        $customerParentId = DB::table('software_menus')->insertGetId([
            'name' => 'Customers & Loyalty',
            'icon' => 'groups',
            'order' => 50,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('software_menus')->insert([
            ['name' => 'Customers', 'route' => 'admin.customers.index', 'parent_id' => $customerParentId, 'order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Membership Tiers', 'route' => 'admin.memberships.index', 'parent_id' => $customerParentId, 'order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Add Reservations & Events
        $reservationParentId = DB::table('software_menus')->insertGetId([
            'name' => 'Reservations',
            'icon' => 'book_online',
            'order' => 60,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('software_menus')->insert([
            ['name' => 'Table Bookings', 'route' => 'admin.reservations.index', 'parent_id' => $reservationParentId, 'order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Company Events', 'route' => 'admin.events.index', 'parent_id' => $reservationParentId, 'order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Add Business Config to Settings
        $settingsMenu = DB::table('software_menus')->where('name', 'Settings')->first();
        if ($settingsMenu) {
            DB::table('software_menus')->insert([
                'name' => 'Business Config',
                'route' => 'admin.business-config.index',
                'parent_id' => $settingsMenu->id,
                'order' => 10,
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
        DB::table('software_menus')->whereIn('name', ['Customers & Loyalty', 'Reservations'])->delete();
        DB::table('software_menus')->where('name', 'Business Config')->delete();
    }
};
