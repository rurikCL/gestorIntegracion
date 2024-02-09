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
        Schema::create('FLU_Flujos', function (Blueprint $table) {
            $table->id('ID');

            $table->dateTime('FechaCreacion');
            $table->integer('EventoCreacionID');
            $table->integer('UsuarioCreacionID');
            $table->dateTime('FechaActualizacion');
            $table->integer('EventoActualizacionID');
            $table->integer('UsuarioActualizacionID');

            $table->string('Nombre');
            $table->string('Descripcion')->nullable();
            $table->string('Tipo')->nullable();
            $table->string('Trigger')->nullable();
            $table->string('Recurrencia')->nullable();
            $table->integer('RecurrenciaValor')->nullable();
            $table->boolean('Activo')->default(1);
            $table->integer('MaxLote')->default(1);
            $table->integer('Reintentos')->default(0)->nullable();
            $table->integer('TiempoEspera')->default(0)->nullable();
            $table->string('Opciones')->nullable();

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
        Schema::dropIfExists('f_l_u__flujos');
    }
};
