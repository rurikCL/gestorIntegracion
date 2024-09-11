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
        Schema::create('APC_InformeOt', function (Blueprint $table) {
            $table->id();
            $table->string('Sucursal')->nullable();
            $table->dateTime('FechaIngreso')->nullable();
            $table->dateTime('FechaCierre')->nullable();
            $table->string('Seccion')->nullable();
            $table->string('TipoOt')->nullable();
            $table->integer('Folio')->nullable();
            $table->string('Recepcionista')->nullable();
            $table->string('Estado')->nullable();
            $table->dateTime('FechaEntrega')->nullable();
            $table->dateTime('FechaEntregaReal')->nullable();
            $table->string('Marca')->nullable();
            $table->string('Nombre')->nullable();
            $table->string('Version')->nullable();
            $table->integer('Anio')->nullable();
            $table->string('Patente')->nullable();
            $table->string('VIN')->nullable();
            $table->string('Dealer')->nullable();
            $table->date('FechaFacturaVehiculo')->nullable();
            $table->string('Color')->nullable();
            $table->integer('KilometrajeActual')->nullable();
            $table->string('Cliente')->nullable();
            $table->string('CompaniaSeguro')->nullable();
            $table->integer('NumeroSiniestro')->nullable();
            $table->integer('TotalServicios')->nullable();
            $table->integer('TotalRepuestos')->nullable();
            $table->integer('Neto')->nullable();
            $table->string('PendienteFacturacion')->nullable();
            $table->string('Grua8Anios')->nullable();
            $table->string('ReingresoATaller')->nullable();
            $table->string('ClientePrioritario')->nullable();
            $table->string('PruebaDeRuta')->nullable();
            $table->string('ComunicarACliente')->nullable();
            $table->string('Campania')->nullable();
            $table->string('ControlDeCalidad')->nullable();
            $table->string('GeneraPresupuesto')->nullable();
            $table->string('Atributo')->nullable();
            $table->integer('Horometro')->nullable();
            $table->longText('ObservacionOt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('APC_InformeOt');
    }
};
