<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    private $table = "API_Solicitudes";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            $table->dateTime('FechaCreacion');
            $table->integer('EventoCreacionID');
            $table->integer('UsuarioCreacionID');
            $table->dateTime('FechaActualizacion');
            $table->integer('EventoActualizacionID');
            $table->integer('UsuarioActualizacionID');

            $table->string('ReferenciaID', 150);
            $table->integer('ProveedorID');
            $table->integer('ApiID');
            $table->integer('Prioridad')->default(1);
            $table->mediumText('Peticion')->nullable();
            $table->mediumText('PeticionHeader')->nullable();
            $table->integer('CodigoRespuesta')->default(1);
            $table->mediumText('Respuesta')->nullable();
            $table->dateTime('FechaPeticion');
            $table->dateTime('FechaResolucion')->nullable();
            $table->boolean('Exito')->default(1);
            $table->boolean('Reprocesa')->default(0);
            $table->integer('FlujoID')->nullable();
            $table->integer('Reintentos')->nullable();

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
        Schema::dropIfExists($this->table);
    }
};
