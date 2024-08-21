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
        Schema::create('APC_Repuestos', function (Blueprint $table) {
            $table->id();
            $table->string('Sucursal')->nullable();
            $table->string('Recepcionista')->nullable();
            $table->string('Cliente')->nullable();
            $table->string('Fecha_Consumo')->nullable();
            $table->integer('Folio_OT')->nullable();
            $table->string('Grupo')->nullable();
            $table->string('Condicion')->nullable();
            $table->string('OT_Estado')->nullable();
            $table->string('OT_Seccion')->nullable();
            $table->string('OT_Tipo')->nullable();
            $table->string('Numero_Vin')->nullable();
            $table->string('Bodega')->nullable();
            $table->string('Ubicacion')->nullable();
            $table->string('Sub_Grupo')->nullable();
            $table->string('Clasificacion')->nullable();
            $table->string('Marca')->nullable();
            $table->string('Version')->nullable();
            $table->string('Placa_Patente')->nullable();
            $table->dateTime('Fecha_Creacion_OT')->nullable();
            $table->string('Tipo_Documento')->nullable();
            $table->integer('Folio_Documento')->nullable();
            $table->string('SKU')->nullable();
            $table->string('Nombre')->nullable();
            $table->integer('Cantidad')->nullable();
            $table->integer('Costo')->nullable();
            $table->integer('Sub_Total')->nullable();
            $table->string('Tipo_Cargo')->nullable();
            $table->dateTime('Fecha_Liquidacion')->nullable();
            $table->dateTime('Fecha_Facturacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('APC_Repuestos');
    }
};
