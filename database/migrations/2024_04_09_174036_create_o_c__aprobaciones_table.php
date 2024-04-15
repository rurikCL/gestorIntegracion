<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('OC_Aprobaciones', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('OrdenCompraID');
            $table->unsignedBigInteger('AprobadorID');
            $table->integer('Nivel');
            $table->integer('Estado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('OC_Aprobaciones');
    }
};
