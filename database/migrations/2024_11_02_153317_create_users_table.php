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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("usuario", 255);
            $table->string("nombre", 600);
            $table->string("apellido", 600);
            $table->string("email", 600);
            $table->string("fono", 600);
            $table->string('password');
            $table->string("foto", 255)->nullable();
            $table->integer("acceso")->default(1);
            $table->string("tipo", 255);
            $table->date("fecha_registro");
            $table->integer("status")->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
