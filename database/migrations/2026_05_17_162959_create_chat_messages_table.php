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
    Schema::create('chat_messages', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
              ->constrained('users')
              ->cascadeOnDelete();

        $table->enum('sender', ['user', 'bot']);
        $table->text('message');

        // Pilihan cepat yang ditampilkan setelah pesan bot
        $table->json('quick_replies')->nullable();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('chat_messages');
}

};
