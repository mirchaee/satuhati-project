<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_sync_data_table.php
public function up(): void
{
    Schema::create('sync_data', function (Blueprint $table) {
        $table->id();

        // wife_id di-set saat istri register
        $table->foreignId('wife_id')
              ->nullable()
              ->constrained('users')
              ->nullOnDelete();

        // husband_id di-set saat suami input kode
        $table->foreignId('husband_id')
              ->nullable()
              ->constrained('users')
              ->nullOnDelete();

        // Kode unik 6 karakter untuk pairing
        $table->string('pairing_code', 10)->unique();

        // false = pending (suami belum input kode)
        // true  = connected (sudah terhubung)
        $table->boolean('status')->default(false);

        $table->timestamp('paired_at')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('sync_data');
}
};
