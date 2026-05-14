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
        Schema::create('branch_settings', function (Blueprint $table) {
            // One-to-one relationship with the branches table
            $table->foreignId('branch_id')
                  ->primary() // Making branch_id the primary key ensures 1-to-1 integrity
                  ->constrained('branches')
                  ->onDelete('cascade');

            // Financial & Tax Settings (Critical for Accounting)
            $table->foreignId('currency_id')->nullable()->constrained('currencies');
            $table->string('vat_registration_no')->nullable();
            $table->decimal('vat_percentage', 5, 2)->default(0.00);
            $table->decimal('service_charge_percentage', 5, 2)->default(0.00);
            $table->boolean('is_vat_inclusive')->default(false);

            // Operational Settings
            $table->string('timezone')->default('UTC');
            $table->string('language_code', 5)->default('en');
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();

            // Branding & Receipt Printing
            $table->string('receipt_header_title')->nullable();
            $table->text('receipt_footer_text')->nullable();
            $table->boolean('show_logo_on_receipt')->default(true);
            $table->string('invoice_prefix')->default('INV');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_settings');
    }
};
