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
        Schema::create('APC_Stock', function (Blueprint $table) {
            $table->id();

            // rellena con los campos necesarios con los siguientes Empresa	Sucursal	Folio Venta	Venta	Estado Venta	Fecha Venta	Tipo Documento (Folio)	Vendedor	Fecha Ingreso	Fecha Facturación	Numero VIN	Marca	Modelo	Versión	Código Versión	Año	Kilometraje	Código Interno	Placa Patente	Condición Vehículo	Color Exterior	Color Interior	Precio Venta Total	Estado AutoPro	Días Stock	Estado Dealer	Bodega	Equipamiento	Numero Motor	Numero Chasis	Proveedor	Fecha Disponibilidad	Factura Compra	Vencimiento Documento	Fecha Compra	Fecha Vencto. Revisión Técnica	N° Propietarios	Folio Retoma	Fecha Retoma	Días Reservado	Precio Compra Neto	Gasto	Accesorios	Total Costo	Precio Lista	Margen	Margen %
            $table->string('Empresa')->nullable();
            $table->string('Sucursal')->nullable();
            $table->integer('Folio_Venta')->nullable();
            $table->integer('Venta')->nullable();
            $table->string('Estado_Venta')->nullable();
            $table->dateTime('Fecha_Venta')->nullable();
            $table->string('Tipo_Documento')->nullable();
            $table->string('Vendedor')->nullable();
            $table->dateTime('Fecha_Ingreso')->nullable();
            $table->dateTime('Fecha_Facturacion')->nullable();
            $table->string('VIN')->nullable();
            $table->string('Marca')->nullable();
            $table->string('Modelo')->nullable();
            $table->string('Version')->nullable();
            $table->string('Codigo_Version')->nullable();
            $table->integer('Anio')->nullable();
            $table->string('Kilometraje')->nullable();
            $table->string('Codigo_Interno')->nullable();
            $table->string('Placa_Patente')->nullable();
            $table->string('Condicion_Vehículo')->nullable();
            $table->string('Color_Exterior')->nullable();
            $table->string('Color_Interior')->nullable();
            $table->integer('Precio_Venta_Total')->nullable();
            $table->string('Estado_AutoPro')->nullable();
            $table->integer('Dias_Stock')->nullable();
            $table->string('Estado_Dealer')->nullable();
            $table->string('Bodega')->nullable();
            $table->string('Equipamiento')->nullable();
            $table->string('Numero_Motor')->nullable();
            $table->string('Numero_Chasis')->nullable();
            $table->string('Proveedor')->nullable();
            $table->dateTime('Fecha_Disponibilidad')->nullable();
            $table->integer('Factura_Compra')->nullable();
            $table->dateTime('Vencimiento_Documento')->nullable();
            $table->dateTime('Fecha_Compra')->nullable();
            $table->dateTime('Fecha_Vencto_Rev_tec')->nullable();
            $table->integer('N_Propietarios')->nullable();
            $table->integer('Folio_Retoma')->nullable();
            $table->dateTime('Fecha_Retoma')->nullable();
            $table->integer('Dias_Reservado')->nullable();
            $table->integer('Precio_Compra_Neto')->nullable();
            $table->integer('Gasto')->nullable();
            $table->string('Accesorios')->nullable();
            $table->integer('Total_Costo')->nullable();
            $table->integer('Precio_Lista')->nullable();
            $table->integer('Margen')->nullable();
            $table->float('Margen_porcentaje')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('APC_Stock');
    }
};
