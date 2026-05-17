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
    Schema::create('diary_entries', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
              ->constrained('users')
              ->cascadeOnDelete();

        $table->string('mood', 50)->nullable();
        $table->text('content')->nullable();

        // Tracking harian
        $table->decimal('water_intake', 4, 1)->default(0); // liter
        $table->string('nutrition_status', 50)->nullable();

        $table->date('entry_date');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('diary_entries');
}
};
