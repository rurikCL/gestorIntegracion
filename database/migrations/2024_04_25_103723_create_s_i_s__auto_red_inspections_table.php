<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('SIS_AutoRedInspections', function (Blueprint $table) {
            $table->id('ID');
            $table->dateTime('FechaCreacion');
            $table->dateTime('FechaSolicitud');
            $table->dateTime('FechaFirma');
            $table->string('Patente');
            $table->string('Marca');
            $table->string('Modelo');
            $table->integer('Anno');
            $table->integer('Kilometraje');
            $table->string('Version');
            $table->string('Color');
            $table->string('SucursalInspeccion');
            $table->string('Inspector');
            $table->integer('CostoTotal');
            $table->integer('CostoTecnico');
            $table->integer('CostoAccesorios');
            $table->integer('KmInspeccion');
            $table->float('PorcentajeCompletado');
            $table->string('Sucursal');
            $table->string('Vendedor');
            $table->string('EmailVendedor');
            $table->string('ArchivoInspeccion', 350);
            $table->integer('IDTransaccion');
            $table->integer('IDInspeccion');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SIS_AutoRedInspections');
    }
};
