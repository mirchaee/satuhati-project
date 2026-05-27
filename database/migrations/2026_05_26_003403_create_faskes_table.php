<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faskes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // rs, klinik, puskesmas
            $table->string('phone')->nullable();
            $table->double('latitude');
            $table->double('longitude');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faskes');
    }
};