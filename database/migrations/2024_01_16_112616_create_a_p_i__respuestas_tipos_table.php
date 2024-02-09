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
        Schema::create('API_RespuestasTipos', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('ApiID');
            $table->string('Tipo'); // success, error
            $table->string('Descripcion');
            $table->string('llave'); // incice de array con mensaje
            $table->string('Mensaje'); // mensaje de respuesta
            $table->boolean('Activo')->default(true);
            $table->boolean('Reprocesa')->default(false);
            $table->string('Trigger',100); // trigger para procesar

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
        Schema::dropIfExists('API_RespuestasTipos');
    }
};
