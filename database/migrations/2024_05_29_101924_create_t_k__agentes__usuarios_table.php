<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('TK_Agentes_Usuarios', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('AgenteID');
            $table->integer('UsuarioID');
            $table->integer('Activo')->default(1);
            $table->integer('EventoCreacionID')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('TK_Agentes_Usuarios');
    }
};
