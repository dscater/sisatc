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
        Schema::create('ejecucion_archivos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("ejecucion_trazabilidad_id");
            $table->string("archivo")->nullable();
            $table->timestamps();
            $table->foreign("ejecucion_trazabilidad_id")->on("ejecucion_trazabilidads")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejecucion_archivos');
    }
};
