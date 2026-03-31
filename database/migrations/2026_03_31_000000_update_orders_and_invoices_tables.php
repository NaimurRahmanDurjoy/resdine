<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Order Master Updates
        if (Schema::hasTable('order_master')) {
            Schema::table('order_master', function (Blueprint $table) {
                if (!Schema::hasColumn('order_master', 'order_number')) {
                    $table->string('order_number')->unique()->after('id');
                }
                if (!Schema::hasColumn('order_master', 'notes')) {
                    $table->text('notes')->nullable()->after('total_amount');
                }
            });
        }

        // 2. Order Details (Items) Updates
        if (Schema::hasTable('order_details')) {
            Schema::table('order_details', function (Blueprint $table) {
                if (!Schema::hasColumn('order_details', 'modifiers')) {
                    $table->json('modifiers')->nullable()->after('item_id');
                }
                if (!Schema::hasColumn('order_details', 'notes')) {
                    $table->text('notes')->nullable()->after('modifiers');
                }
                if (!Schema::hasColumn('order_details', 'unit_price')) {
                    $table->decimal('unit_price', 10, 2)->default(0)->after('price');
                }
                if (!Schema::hasColumn('order_details', 'total_price')) {
                    $table->decimal('total_price', 10, 2)->default(0)->after('total');
                }
            });
        }

        // 3. Order Payments Updates (Split Payments Support)
        if (Schema::hasTable('order_payments')) {
            Schema::table('order_payments', function (Blueprint $table) {
                if (!Schema::hasColumn('order_payments', 'amount')) {
                    $table->decimal('amount', 10, 2)->default(0)->after('method');
                }
                if (!Schema::hasColumn('order_payments', 'transaction_reference')) {
                    $table->string('transaction_reference')->nullable()->after('amount');
                }
                if (!Schema::hasColumn('order_payments', 'status')) {
                    $table->tinyInteger('status')->default(1)->comment('1=paid,2=partial')->after('transaction_reference');
                }
                if (!Schema::hasColumn('order_payments', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable()->after('status');
                }
            });
        }

        // 4. Invoice Master Updates
        if (Schema::hasTable('invoice_master')) {
            Schema::table('invoice_master', function (Blueprint $table) {
                if (!Schema::hasColumn('invoice_master', 'issued_at')) {
                    $table->timestamp('issued_at')->nullable()->after('status');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('order_master', function (Blueprint $table) {
            $table->dropColumn(['order_number', 'notes']);
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn(['modifiers', 'notes', 'unit_price', 'total_price']);
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropColumn(['amount', 'transaction_reference', 'status', 'paid_at']);
        });

        Schema::table('invoice_master', function (Blueprint $table) {
            $table->dropColumn(['issued_at']);
        });
    }
};
