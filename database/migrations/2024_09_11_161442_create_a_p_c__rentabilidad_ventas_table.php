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
        Schema::create('APC_RentabilidadVenta', function (Blueprint $table) {
            $table->id();
            $table->string('Sucursal')->nullable();
            $table->date('FechaFacturacion')->nullable();
            $table->string('TipoDocumento')->nullable();
            $table->integer('Folio')->nullable();
            $table->string('Vendedor')->nullable();
            $table->string('Cliente')->nullable();
            $table->integer('Rut')->nullable();
            $table->string('Digito')->nullable();
            $table->string('CodigoUnicoExtranjero')->nullable();
            $table->string('SKU')->nullable();
            $table->string('NombreSKU')->nullable();
            $table->string('Marca')->nullable();
            $table->string('GrupoSKU')->nullable();
            $table->string('SubGrupoSKU')->nullable();
            $table->string('UnidadMediaSKU')->nullable();
            $table->integer('Cantidad')->nullable();
            $table->integer('Venta')->nullable();
            $table->integer('Costo')->nullable();
            $table->integer('Margen')->nullable();
            $table->float('PorcentajeMargen')->nullable();

            $table->string('')->nullable();
            $table->string('')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('APC_RentabilidadVenta');
    }
};
