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
        Schema::create('FLU_Homologacion', function (Blueprint $table) {
            $table->id('ID');

            $table->dateTime('FechaCreacion');
            $table->integer('EventoCreacionID');
            $table->integer('UsuarioCreacionID');
            $table->dateTime('FechaActualizacion')->nullable();
            $table->integer('EventoActualizacionID')->nullable();
            $table->integer('UsuarioActualizacionID')->nullable();

            $table->string('CodHomologacion', 100)->nullable();
            $table->integer('FlujoID');
            $table->string('ValorIdentificador');
            $table->string('ValorRespuesta');
            $table->string('ValorNombre')->nullable();
            $table->integer('Activo')->default(1);

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
        Schema::dropIfExists('FLU_Homologacion');
    }
};
