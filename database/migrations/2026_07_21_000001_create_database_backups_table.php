<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('database_backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('db_host')->default('127.0.0.1');
            $table->string('db_port')->default('3306');
            $table->string('db_name');
            $table->string('db_username');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->default(0); // bytes
            $table->enum('backup_type', ['data_only', 'structure_only', 'complete'])->default('complete');
            $table->enum('status', ['completed', 'failed'])->default('completed');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('database_backups');
    }
};
