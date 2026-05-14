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

        // 2. Create Core Submenus
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

        // 3. Create Accounts Report Parent (Inside Accounting)
        $reportsParent = SoftwareMenu::updateOrCreate(
            ['name' => 'Accounts Report'],
            [
                'parent_id' => $accounting->id,
                'icon' => 'analytics',
                'route' => null,
                'order' => 3,
                'is_active' => true,
            ]
        );

        // 4. Create Submenus under Reports Parent
        $reportSubmenus = [
            [
                'name' => 'General Ledger',
                'route' => 'admin.accounts.reports.ledger',
                'icon' => 'menu_book',
                'order' => 1,
            ],
            [
                'name' => 'Trial Balance',
                'route' => 'admin.accounts.reports.trial-balance',
                'icon' => 'account_balance_wallet',
                'order' => 2,
            ],
            [
                'name' => 'Profit & Loss',
                'route' => 'admin.accounts.reports.profit-loss',
                'icon' => 'monitoring',
                'order' => 3,
            ],
        ];

        foreach ($reportSubmenus as $sub) {
            SoftwareMenu::updateOrCreate(
                ['route' => $sub['route']],
                [
                    'name' => $sub['name'],
                    'icon' => $sub['icon'],
                    'parent_id' => $reportsParent->id,
                    'order' => $sub['order'],
                    'is_active' => true,
                ]
            );
        }

        // 5. Grant Access
        $users = \App\Models\User::all();
        $menus = SoftwareMenu::where('parent_id', $accounting->id)
            ->orWhere('parent_id', $reportsParent->id)
            ->orWhere('id', $accounting->id)
            ->get();

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
