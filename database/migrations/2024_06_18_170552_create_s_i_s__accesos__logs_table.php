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
        Schema::create('SIS_Accesos_Logs', function (Blueprint $table) {
            $table->id();
            $table->integer('UsuarioID');
            $table->string('IP', 50);
            $table->string('Navegador', 255);
            $table->string('SistemaOperativo', 255);
            $table->string('Dispositivo', 255);
            $table->string('Tipo', 50);
            $table->string('Accion', 50);
            $table->string('Descripcion', 255);
            $table->string('Tabla', 50);
            $table->integer('RegistroID');
            $table->string('Registro', 255);
            $table->string('Anterior', 255);
            $table->string('Nuevo', 255);
            $table->string('Estado', 50);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SIS_Accesos_Logs');
    }
};
