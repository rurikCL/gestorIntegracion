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
        Schema::create('APC_RentabilidadOt', function (Blueprint $table) {
            $table->id();
            //
            $table->string('Sucursal');
            $table->dateTime('FechaFacturacion');
            $table->string('TipoDocumento');
            $table->string('TipoTrabajoOT');
            $table->integer('Folio');
            $table->integer('FolioOT');
            $table->dateTime('FechaOT');
            $table->string('OTTipo');
            $table->string('OTSeccion');
            $table->string('ClienteOT');
            $table->string('ClienteRut');
            $table->string('ClienteDireccion');
            $table->string('ClienteComuna');
            $table->string('ClienteCiudad');
            $table->string('ClienteTelefonos');
            $table->string('ClienteEmail');
            $table->string('TipoCargoServicio');
            $table->integer('VentaMO');
            $table->integer('CostoMO');
            $table->integer('MargenMO');
            $table->float('MargenMOPorcentaje');
            $table->integer('TotalInsumos');
            $table->integer('TotalSeguro');
            $table->integer('VentaCarroceria');
            $table->integer('CostoCarroceria');
            $table->integer('MargenCarroceria');
            $table->float('MargenCarroceriaPorcentaje');
            $table->integer('VentaServicioTerceros');
            $table->integer('CostoServicioTerceros');
            $table->integer('MargenServicioTerceros');
            $table->integer('MargenTercerosPorcentaje');
            $table->integer('VentaRepuestos');
            $table->integer('CostoRepuestos');
            $table->integer('MargenRepuestos');
            $table->float('MargenRepuestosPorcentaje');
            $table->integer('TotalMaterialML');
            $table->integer('CostoMaterialML');
            $table->integer('MargenMaterialML');
            $table->integer('MargenMaterialPje');
            $table->integer('VentaLubricantes');
            $table->integer('CostoLubricantes');
            $table->integer('MargenLubricantes');
            $table->float('MargenLubricantesPorcentaje');
            $table->integer('TotalDeducible');
            $table->integer('TotalVenta');
            $table->integer('TotalCosto');
            $table->integer('TotalMargen');
            $table->float('TotalMargenPorcentaje');
            $table->integer('TotalNetoFacturado');
            $table->integer('Descuestos');
            $table->string('ClienteNombre2');
            $table->string('ClienteRut2');
            $table->string('ClienteDireccion2');
            $table->string('ClienteComuna2');
            $table->string('ClienteCiudad2');
            $table->string('ClienteTelefonos2');
            $table->string('ClienteEmail2');
            $table->string('Marca');
            $table->string('Modelo');
            $table->string('NumeroVIN');
            $table->string('Chasis');
            $table->string('Patente');
            $table->integer('Kilometraje');
            $table->string('Mecanico');
            $table->string('Recepcionista');
            $table->integer('FolioGarantia');
            $table->string('TipoMantención');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('APC_RentabilidadOt');
    }
};
