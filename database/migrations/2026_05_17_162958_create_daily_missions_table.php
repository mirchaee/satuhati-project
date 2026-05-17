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
    Schema::create('daily_missions', function (Blueprint $table) {
        $table->id();

        // Milik suami siapa
        $table->foreignId('user_id')
              ->constrained('users')
              ->cascadeOnDelete();

        $table->string('title');
        $table->text('description')->nullable();
        $table->boolean('is_completed')->default(false);
        $table->date('mission_date');

        // Misi disesuaikan dengan usia kandungan berapa
        $table->unsignedTinyInteger('target_week')->nullable();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('daily_missions');
}
};
