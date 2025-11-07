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
                Schema::create('publicacion', function (Blueprint $table) {
            $table->id('id_publicacion');
            $table->text('tipo_publicacion');
            $table->string('titulo');
            $table->text('contenido');
            $table->date('fecha_publicacion');
            $table->string('imagen_destacada');
            $table->string('numero_vistas');
            $table->string('estado')->default('activo');

            // relacion con la tabla de usuario

            $table->unsignedBigInteger('id_usuario');
             $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade');
             $table->unsignedBigInteger('id_categoria')->references('id_categoria')->on('categoria')->onDelete('cascade');
             $table->timestamps();
    });
}
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicacion');
    }
};
