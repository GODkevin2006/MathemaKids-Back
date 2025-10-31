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
         Schema::create('podcats', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nombre')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('categoria')->nullable();

            // relacion con la tabla de usuario

            $table->string('id_usuario')->nullable();
             $table->foreign('id_usuario')->references('id')->on ('usuario')->onDelete('cascade');
             $table->timestamps();


            
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('podcats');
    }
};
