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
    Schema::create('symptoms', function (Blueprint $table) {
        $table->id();
        $table->string('name', 100);             // "Mual", "Pusing", dll
        $table->string('category', 50);          // "fisik", "emosional"
        $table->unsignedTinyInteger('weight');   // Bobot untuk kalkulasi risiko
        $table->boolean('is_critical')->default(false); // Langsung trigger waspada?
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('symptoms');
}
};
