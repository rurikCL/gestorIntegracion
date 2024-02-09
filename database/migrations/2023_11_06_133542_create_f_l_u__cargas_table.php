<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('FLU_Cargas', function (Blueprint $table) {
            $table->id('ID');
            $table->timestamps();
            $table->dateTime('FechaCreacion');
            $table->integer('EventoCreacionID');
            $table->integer('UsuarioCreacionID');
            $table->dateTime('FechaActualizacion')->nullable();
            $table->integer('EventoActualizacionID')->nullable();
            $table->integer('UsuarioActualizacionID')->nullable();

            $table->integer('ID_Flujo')->nullable();
            $table->dateTime('FechaCarga');
            $table->integer('Registros')->nullable();
            $table->integer('RegistrosCargados')->nullable();
            $table->integer('RegistrosFallidos')->nullable();
            $table->integer('Estado')->default(0);
            $table->string('File', 300)->nullable();
            $table->string('FilePath', 300)->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_l_u__cargas');
    }
};
