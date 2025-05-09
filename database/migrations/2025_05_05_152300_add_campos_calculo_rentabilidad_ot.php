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
            $table->integer('CalculoTotalAfectoComision')->nullable()->unsigned();
            $table->integer('CalculoIngresoMO')->nullable()->unsigned();
            $table->integer('CalculoIngresoRepuestos')->nullable()->unsigned();
            $table->integer('CalculoIngresoTerDedu')->nullable()->unsigned();
            $table->integer('CalculoIngresoTotal')->nullable()->unsigned();
            $table->integer('CalculoCostoMO')->nullable()->unsigned();
            $table->integer('CalculoCostoRepuestos2')->nullable()->unsigned();
            $table->integer('CalculoCostoTerDedu')->nullable()->unsigned();
            $table->integer('CalculoCostoTotal')->nullable()->unsigned();
            $table->integer('CalculoMargenMo')->nullable()->unsigned();
            $table->integer('CalculoMargenRepuestos')->nullable()->unsigned();
            $table->integer('CalculoMargenTerDedu')->nullable()->unsigned();
            $table->integer('CalculoMargenTotal')->nullable()->unsigned();
            $table->integer('CalculoOtsTotal')->nullable()->unsigned();
            $table->integer('CalculoCostoLogistica')->nullable()->unsigned();
            $table->integer('CalculoCostoInsumos')->nullable()->unsigned();
            $table->integer('CalculoCostoPintura')->nullable()->unsigned();
            $table->integer('CalculoCostoOtros')->nullable()->unsigned();
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
