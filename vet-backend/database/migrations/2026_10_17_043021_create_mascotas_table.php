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
        Schema::create('mascotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Dueño de la mascota
            $table->string('nombre');
            $table->string('especie'); // Ej: Perro, Gato, Ave
            $table->string('raza')->nullable();
            $table->integer('edad_meses')->nullable();
            $table->decimal('peso', 5, 2)->nullable(); // Ej: 12.50 kg
            $table->string('foto_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mascotas');
    }
};