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
    Schema::create('assessment_symptoms', function (Blueprint $table) {
        $table->id();

        $table->foreignId('assessment_id')
              ->constrained('health_assessments')
              ->cascadeOnDelete();

        $table->foreignId('symptom_id')
              ->constrained('symptoms')
              ->cascadeOnDelete();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('assessment_symptoms');
}
};
