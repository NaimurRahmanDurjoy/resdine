<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed default account types
        DB::table('acc_account_types')->insert([

            [
                'id' => 1,
                'name' => 'Asset',
                'code' => 'AST',
                'description' => 'Resources owned by the business',
                'status' => 1,
            ],

            [
                'id' => 2,
                'name' => 'Liability',
                'code' => 'LIA',
                'description' => 'Business obligations and dues',
                'status' => 1,
            ],

            [
                'id' => 3,
                'name' => 'Equity',
                'code' => 'EQT',
                'description' => 'Owner investment and retained earnings',
                'status' => 1,
            ],

            [
                'id' => 4,
                'name' => 'Revenue',
                'code' => 'REV',
                'description' => 'Business income accounts',
                'status' => 1,
            ],

            [
                'id' => 5,
                'name' => 'Expense',
                'code' => 'EXP',
                'description' => 'Business expense accounts',
                'status' => 1,
            ],

        ]);

        // Seed default voucher types
        DB::table('acc_voucher_types')->insert([

            [
                'id' => 1,
                'name' => 'Journal',
                'prefix' => 'JV',
                'starting_number' => 1,
                'status' => 1,
            ],

            [
                'id' => 2,
                'name' => 'Receipt',
                'prefix' => 'RV',
                'starting_number' => 1,
                'status' => 1,
            ],

            [
                'id' => 3,
                'name' => 'Payment',
                'prefix' => 'PV',
                'starting_number' => 1,
                'status' => 1,
            ],

            [
                'id' => 4,
                'name' => 'Contra',
                'prefix' => 'CV',
                'starting_number' => 1,
                'status' => 1,
            ],

            [
                'id' => 5,
                'name' => 'Sales',
                'prefix' => 'SV',
                'starting_number' => 1,
                'status' => 1,
            ],

            [
                'id' => 6,
                'name' => 'Purchase',
                'prefix' => 'PRV',
                'starting_number' => 1,
                'status' => 1,
            ],

        ]);

        // Seed default fiscal year
        DB::table('acc_fiscal_years')->insert([

            [
                'id' => 1,
                'name' => '2025-2026',
                'start_date' => '2025-07-01',
                'end_date' => '2026-06-30',
                'is_active' => 1,
                'is_closed' => 0,
                'description' => 'Default active fiscal year',
            ]

        ]);

        // Seed default chart of accounts
        DB::table('acc_chart_of_accounts')->insert([

            /*
            |--------------------------------------------------------------------------
            | ASSETS
            |--------------------------------------------------------------------------
            */

            [
                'id' => 1,
                'code' => '1000',
                'name' => 'Assets',
                'parent_id' => null,
                'account_type_id' => 1,
                'opening_balance' => 0,
                'balance_type' => 'D',
                'allow_transaction' => 0,
                'is_system' => 1,
                'status' => 1,
            ],

            [
                'id' => 2,
                'code' => '1001',
                'name' => 'Cash',
                'parent_id' => 1,
                'account_type_id' => 1,
                'opening_balance' => 0,
                'balance_type' => 'D',
                'allow_transaction' => 1,
                'is_system' => 1,
                'status' => 1,
            ],

            [
                'id' => 3,
                'code' => '1002',
                'name' => 'Bank',
                'parent_id' => 1,
                'account_type_id' => 1,
                'opening_balance' => 0,
                'balance_type' => 'D',
                'allow_transaction' => 1,
                'is_system' => 1,
                'status' => 1,
            ],

            [
                'id' => 4,
                'code' => '1003',
                'name' => 'Inventory',
                'parent_id' => 1,
                'account_type_id' => 1,
                'opening_balance' => 0,
                'balance_type' => 'D',
                'allow_transaction' => 1,
                'is_system' => 1,
                'status' => 1,
            ],

            /*
            |--------------------------------------------------------------------------
            | LIABILITIES
            |--------------------------------------------------------------------------
            */

            [
                'id' => 5,
                'code' => '2000',
                'name' => 'Liabilities',
                'parent_id' => null,
                'account_type_id' => 2,
                'opening_balance' => 0,
                'balance_type' => 'C',
                'allow_transaction' => 0,
                'is_system' => 1,
                'status' => 1,
            ],

            [
                'id' => 6,
                'code' => '2001',
                'name' => 'Accounts Payable',
                'parent_id' => 5,
                'account_type_id' => 2,
                'opening_balance' => 0,
                'balance_type' => 'C',
                'allow_transaction' => 1,
                'is_system' => 1,
                'status' => 1,
            ],

            /*
            |--------------------------------------------------------------------------
            | EQUITY
            |--------------------------------------------------------------------------
            */

            [
                'id' => 7,
                'code' => '3000',
                'name' => 'Equity',
                'parent_id' => null,
                'account_type_id' => 3,
                'opening_balance' => 0,
                'balance_type' => 'C',
                'allow_transaction' => 0,
                'is_system' => 1,
                'status' => 1,
            ],

            [
                'id' => 8,
                'code' => '3001',
                'name' => 'Owner Capital',
                'parent_id' => 7,
                'account_type_id' => 3,
                'opening_balance' => 0,
                'balance_type' => 'C',
                'allow_transaction' => 1,
                'is_system' => 1,
                'status' => 1,
            ],

            /*
            |--------------------------------------------------------------------------
            | REVENUE
            |--------------------------------------------------------------------------
            */

            [
                'id' => 9,
                'code' => '4000',
                'name' => 'Revenue',
                'parent_id' => null,
                'account_type_id' => 4,
                'opening_balance' => 0,
                'balance_type' => 'C',
                'allow_transaction' => 0,
                'is_system' => 1,
                'status' => 1,
            ],

            [
                'id' => 10,
                'code' => '4001',
                'name' => 'Food Sales',
                'parent_id' => 9,
                'account_type_id' => 4,
                'opening_balance' => 0,
                'balance_type' => 'C',
                'allow_transaction' => 1,
                'is_system' => 1,
                'status' => 1,
            ],

            /*
            |--------------------------------------------------------------------------
            | EXPENSES
            |--------------------------------------------------------------------------
            */

            [
                'id' => 11,
                'code' => '5000',
                'name' => 'Expenses',
                'parent_id' => null,
                'account_type_id' => 5,
                'opening_balance' => 0,
                'balance_type' => 'D',
                'allow_transaction' => 0,
                'is_system' => 1,
                'status' => 1,
            ],

            [
                'id' => 12,
                'code' => '5001',
                'name' => 'Salary Expense',
                'parent_id' => 11,
                'account_type_id' => 5,
                'opening_balance' => 0,
                'balance_type' => 'D',
                'allow_transaction' => 1,
                'is_system' => 1,
                'status' => 1,
            ],

        ]);
    }
}
