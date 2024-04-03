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
        Schema::create('MA_IndicadorMonetario', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('Monto');
            $table->string('Tipo'); // success, error
            $table->dateTime('FechaIndicador');
            $table->string('Fuente');
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
        //
    }
};
