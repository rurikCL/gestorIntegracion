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
        Schema::table('APC_RentabilidadOt', function (Blueprint $table) {
            //
            $table->string('Gerencia')->nullable()->after('TipoMantención');
            $table->string('MarcaPompeyo')->nullable();
            $table->string('SucursalPompeyo')->nullable();
            $table->integer('CostoAjustado')->nullable()->unsigned();
            $table->integer('MargenCostoAjustado')->nullable()->unsigned();
            $table->integer('OtReal')->nullable();
            $table->integer('Patentes')->nullable();
            $table->string('TipoOtCorregida')->nullable();
            $table->string('Pagado')->nullable();
            $table->integer('TotalAfectoComision')->nullable()->unsigned();
            $table->integer('IngresoMO')->nullable()->unsigned();
            $table->integer('IngresoRepuestos')->nullable()->unsigned();
            $table->integer('IngresoTerDedu')->nullable()->unsigned();
            $table->integer('IngresoTotal')->nullable()->unsigned();
            $table->integer('CostoMO')->nullable()->unsigned();
            $table->integer('CostoRepuestos')->nullable()->unsigned();
            $table->integer('CostoTerDedu')->nullable()->unsigned();
            $table->integer('CostoTotal')->nullable()->unsigned();
            $table->integer('MargenMo')->nullable()->unsigned();
            $table->integer('MargenRepuestos')->nullable()->unsigned();
            $table->integer('MargenTerDedu')->nullable()->unsigned();
            $table->integer('MargenTotal')->nullable()->unsigned();
            $table->integer('OtsTotal')->nullable()->unsigned();
            $table->integer('CostoLogistica')->nullable()->unsigned();
            $table->integer('CostoInsumos')->nullable()->unsigned();
            $table->integer('CostoPintura')->nullable()->unsigned();
            $table->integer('CostoOtros')->nullable()->unsigned();
            $table->float('NC')->nullable();
            $table->float('NCPorcentaje')->nullable();
            $table->integer('Margen2')->nullable()->unsigned();
            $table->integer('MetaComercial')->nullable()->unsigned();
            $table->integer('MetaComercialPorcentaje')->nullable()->unsigned();

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('APC_RentabilidadOt', function (Blueprint $table) {
            //
        });
    }
};
