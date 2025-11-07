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
          Schema::create('contenido_proyecto', function (Blueprint $table) {
            $table->id('id_contenido');
            $table->text('contenido');
            $table->date('fecha_creacion');
            $table->date('fecha_actualizacion');
            $table->string('archivo_adjunto');
            $table->string('estado')->default('activo');

            // relacion con la tabla de usuario

            $table->unsignedBigInteger('id_proyecto');
             $table->foreign('id_proyecto')->references('id_proyecto')->on('proyecto')->onDelete('cascade');
             $table->timestamps();//
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('contenido_proyecto');
    }
};
