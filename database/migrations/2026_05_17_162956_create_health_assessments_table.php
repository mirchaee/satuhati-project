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
    Schema::create('health_assessments', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
              ->constrained('users')
              ->cascadeOnDelete();

        // Input dari conversational UI
        $table->string('mood_status', 50);
        $table->text('notes')->nullable();
        $table->unsignedTinyInteger('pain_scale')->nullable(); // 1-10

        // Vital signs (opsional)
        $table->decimal('weight_kg', 5, 2)->nullable();
        $table->string('blood_pressure', 10)->nullable(); // "120/80"
        $table->unsignedSmallInteger('fetal_heart_rate')->nullable(); // bpm

        // Hasil kalkulasi sistem
        $table->enum('risk_level', ['Aman', 'Waspada', 'Bahaya']);
        $table->unsignedTinyInteger('risk_score')->default(0); // 0-100

        // Sudah dikirim ke suami?
        $table->boolean('is_synced')->default(false);
        $table->boolean('is_emergency')->default(false);

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('health_assessments');
}

};
