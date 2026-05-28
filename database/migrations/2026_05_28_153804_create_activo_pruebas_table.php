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
        Schema::create('activo_pruebas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("activo_id");
            $table->text("descripcion");
            $table->string("modulo");
            $table->string("prueba");
            $table->unsignedBigInteger("user_id");
            $table->date("fecha");
            $table->time("hora");
            $table->timestamps();

            $table->foreign("activo_id")->on("activos")->references("id");
            $table->foreign("user_id")->on("users")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activo_pruebas');
    }
};
