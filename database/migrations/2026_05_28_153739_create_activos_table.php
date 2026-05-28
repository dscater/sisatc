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
        Schema::create('activos', function (Blueprint $table) {
            $table->id();
            $table->string("codigo")->unique();
            $table->string("nombre", 600);
            $table->text("descripcion");
            $table->unsignedBigInteger("tipo_activo_id");
            $table->string("version");
            $table->unsignedBigInteger("user_id");
            $table->date("fecha_registro");
            $table->timestamps();

            $table->foreign("tipo_activo_id")->on("tipo_activos")->references("id");
            $table->foreign("user_id")->on("users")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activos');
    }
};
