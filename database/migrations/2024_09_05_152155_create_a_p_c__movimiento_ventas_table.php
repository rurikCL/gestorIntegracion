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
        Schema::create('APC_MovimientoVentas', function (Blueprint $table) {
            $table->id();
            $table->integer('Detalle')->nullable();
            $table->string('Sucursal')->nullable();
            $table->string('TipoDocumento')->nullable();
            $table->integer('Folio')->nullable();
            $table->dateTime('FechaDocumento')->nullable();
            $table->string('Estado')->nullable();
            $table->string('Vendedor',300)->nullable();
            $table->string('Cliente',300)->nullable();
            $table->string('Sku')->nullable();
            $table->string('Nombre',300)->nullable();
            $table->string('Grupo')->nullable();
            $table->string('Bodega')->nullable();
            $table->string('Ubicacion',300)->nullable();
            $table->string('UnidadMedida')->nullable();
            $table->integer('Cantidad')->nullable();
            $table->integer('PrecioUnitario')->nullable();
            $table->integer('Valor')->nullable();
            $table->integer('SubTotal')->nullable();
            $table->string('TipoTransaccion')->nullable();
            $table->string('SubGrupo')->nullable();
            $table->integer('Venta')->nullable();
            $table->string('UsuarioCreacion',300)->nullable();
            $table->dateTime('FechaCreacion')->nullable();
            $table->string('FolioOt')->nullable();
            $table->string('DescripcionOt', 300)->nullable();
            $table->string('TipoCargo')->nullable();
            $table->dateTime('FechaEmision')->nullable();
            $table->string('RutCliente')->nullable();
            $table->string('SeccionOt')->nullable();
            $table->integer('NroNotaVenta')->nullable();
            $table->integer('FolioFactura')->nullable();
            $table->dateTime('FechaFacturacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('APC_MovimientoVentas');
    }
};
