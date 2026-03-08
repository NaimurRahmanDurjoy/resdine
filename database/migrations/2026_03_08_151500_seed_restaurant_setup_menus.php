<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SoftwareMenu;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get Settings Menu
        $settingsMenu = SoftwareMenu::where('name', 'Settings')->first();
        
        if (!$settingsMenu) {
            $settingsMenu = SoftwareMenu::create([
                'name' => 'Settings',
                'route' => 'admin.settings.index',
                'icon' => 'settings',
                'order' => 100,
            ]);
        }

        // Create Restaurant Setup Parent
        $setupParent = SoftwareMenu::create([
            'name' => 'Restaurant Setup',
            'icon' => 'settings_suggest',
            'parent_id' => $settingsMenu->id,
            'order' => 1,
        ]);

        // Create Submenus
        SoftwareMenu::create([
            'name' => 'Branches',
            'route' => 'admin.settings.restaurant-setup.branches.index',
            'icon' => 'store',
            'parent_id' => $setupParent->id,
            'order' => 1,
        ]);

        SoftwareMenu::create([
            'name' => 'Kitchen/Res Dept',
            'route' => 'admin.settings.restaurant-setup.res-departments.index',
            'icon' => 'kitchen',
            'parent_id' => $setupParent->id,
            'order' => 2,
        ]);

        SoftwareMenu::create([
            'name' => 'Staff Dept',
            'route' => 'admin.settings.restaurant-setup.staff-departments.index',
            'icon' => 'groups',
            'parent_id' => $setupParent->id,
            'order' => 3,
        ]);

        SoftwareMenu::create([
            'name' => 'Tables & Sections',
            'route' => 'admin.settings.restaurant-setup.tables.index',
            'icon' => 'table_restaurant',
            'parent_id' => $setupParent->id,
            'order' => 4,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $setupParent = SoftwareMenu::where('name', 'Restaurant Setup')->first();
        if ($setupParent) {
            SoftwareMenu::where('parent_id', $setupParent->id)->delete();
            $setupParent->delete();
        }
    }
};
