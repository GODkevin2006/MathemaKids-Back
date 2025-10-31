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
            $table->string('id')->primary();
            $table->text('tipo_publicacion')->nullable();
            $table->string('titulo')->nullable();
            $table->text('contenido')->nullable();
            $table->date('fecha_publicacion')->nullable();
            $table->string('imagen_destacada')->nullable();
            $table->string('vistas')->nullable();

            // relacion con la tabla de usuario

            $table->string('id_usuario')->nullable();
             $table->foreign('id_usuario')->references('id')->on ('usuario')->onDelete('cascade');
             $table->integer('id_categoria')->references('id')->on ('categoria')->onDelete('cascade');
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
