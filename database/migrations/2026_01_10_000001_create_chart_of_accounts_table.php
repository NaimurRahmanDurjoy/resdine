<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('acc_chart_of_accounts', function (Blueprint $table) {

            $table->id();
            $table->string('code')->unique();  // Unique code for the account, e.g., "1000", "2000-01", etc.
            $table->string('name'); // Account name, e.g., "Cash", "Accounts Payable", etc.
            $table->foreignId('parent_id')->nullable()->constrained('acc_chart_of_accounts')->nullOnDelete();  // Self-referencing foreign key for hierarchical structure
            $table->foreignId('account_type_id')->constrained('acc_account_types')->cascadeOnDelete();  // Foreign key to account types
            $table->decimal('opening_balance', 15, 2)->default(0); // Initial balance of the account

            // Debit or Credit Nature
            $table->enum('balance_type', ['D', 'C']);

            // Can transactions be posted?
            $table->boolean('allow_transaction')
                ->default(true);

            // System-generated account?
            $table->boolean('is_system')->default(false);
            $table->boolean('status')->default(true);

            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('account_type_id');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_chart_of_accounts');
    }
};
