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
        Schema::create('proyecto', function (Blueprint $table) {
            $table->id('id_proyecto');
            $table->string('nombre')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('imagen_portada')->nullable();

            // relacion con la tabla de usuario

            $table->unsignedBigInteger('id_usuario');
             $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade');
             $table->timestamps();


            
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('proyecto');
    }
};
