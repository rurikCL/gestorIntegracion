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
        Schema::create('APC_RentabilidadSku', function (Blueprint $table) {
            $table->id();
            $table->string('Sucursal')->nullable();
            $table->string('TipoDocumento')->nullable();
            $table->integer('Folio')->nullable();
            $table->date('FechaFacturacion')->nullable();
            $table->integer('FolioOt')->nullable();
            $table->string('Servicio')->nullable();
            $table->string('SKU')->nullable();
            $table->string('Nombre')->nullable();
            $table->string('Grupo')->nullable();
            $table->string('SubGrupo')->nullable();
            $table->string('Marca')->nullable();
            $table->string('Medida')->nullable();
            $table->string('Cantidad')->nullable();
            $table->string('Mecanico')->nullable();
            $table->integer('Venta')->nullable();
            $table->integer('Costo')->nullable();
            $table->integer('Margen')->nullable();
            $table->float('Porcentaje')->nullable();
            $table->string('Recepcionista')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('APC_RentabilidadSku');
    }
};
