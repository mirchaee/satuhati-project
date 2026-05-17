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
    Schema::create('emergency_contacts', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
              ->constrained('users')
              ->cascadeOnDelete();

        $table->string('name');
        $table->string('phone', 20);
        $table->string('relation', 50); // "Suami", "Orang Tua", dll
        $table->boolean('is_primary')->default(false);

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('emergency_contacts');
}

};
