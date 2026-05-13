<?php

namespace Database\Seeders;

use App\Models\SoftwareMenu;
use App\Models\SoftwareMenuAction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountingMenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Parent Menu
        $accounting = SoftwareMenu::updateOrCreate(
            ['name' => 'Accounting'],
            [
                'icon' => 'account_balance',
                'route' => null,
                'order' => 10,
                'is_active' => true,
            ]
        );

        // 2. Create Submenus
        $submenus = [
            [
                'name' => 'Chart of Accounts',
                'route' => 'admin.accounts.coa.index',
                'icon' => 'account_tree',
                'order' => 1,
            ],
            [
                'name' => 'Voucher Entry',
                'route' => 'admin.accounts.vouchers.index',
                'icon' => 'payments',
                'order' => 2,
            ],
            [
                'name' => 'Reports',
                'route' => 'admin.accounts.reports.ledger',
                'icon' => 'analytics',
                'order' => 3,
            ],
            /*[
                'name' => 'General Ledger',
                'route' => 'admin.accounts.ledger.index',
                'icon' => 'menu_book',
                'order' => 3,
            ]*/
        ];

        foreach ($submenus as $sub) {
            SoftwareMenu::updateOrCreate(
                ['route' => $sub['route']],
                [
                    'name' => $sub['name'],
                    'icon' => $sub['icon'],
                    'parent_id' => $accounting->id,
                    'order' => $sub['order'],
                    'is_active' => true,
                ]
            );
        }

        // 3. Optional: Grant access to all users for testing (In production, use Roles)
        $users = \App\Models\User::all();
        $menus = SoftwareMenu::where('name', 'Accounting')->orWhere('parent_id', $accounting->id)->get();

        foreach ($users as $user) {
            foreach ($menus as $menu) {
                DB::table('software_menu_access')->updateOrInsert([
                    'menu_id' => $menu->id,
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
