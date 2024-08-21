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
        Schema::create('APC_Sku', function (Blueprint $table) {
            $table->id();
            $table->string('Sucursal')->nullable();
            $table->string('Bodega')->nullable();
            $table->string('Ubicacion')->nullable();
            $table->string('Cod_Sku')->nullable();
            $table->string('Sku')->nullable();
            $table->integer('Saldo')->nullable();
            $table->integer('Cup')->nullable();
            $table->integer('Total')->nullable();
            $table->string('Grupo')->nullable();
            $table->string('Sub_Grupo')->nullable();
            $table->string('Marca')->nullable();
            $table->string('Clasificacion')->nullable();
            $table->string('Condicion')->nullable();
            $table->string('Categoria')->nullable();
            $table->dateTime('Fecha_Primera_Compra')->nullable();
            $table->dateTime('Fecha_Ultima_Compra')->nullable();
            $table->dateTime('Fecha_Ultima_Venta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('APC_Sku');
    }
};
