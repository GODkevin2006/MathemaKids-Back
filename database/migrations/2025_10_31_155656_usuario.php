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
        Schema::create('usuario', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nombres')->nullable();
            $table->string('apellidos')->nullable();
            $table->string('correo')->unique();
            $table->string('contraseña')->nullable();
            $table->string('token_verificado')->nullable();
            
            // relacion con la tabla rol

        $table->string('id_rol')->nullable();
        $table->foreign('id_rol')->references('id')->on ('rol')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
