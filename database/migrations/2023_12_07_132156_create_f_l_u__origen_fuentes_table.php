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
        Schema::create('FLU_OrigenFuente', function (Blueprint $table) {
            $table->id('ID');
            $table->string('OrigenFuente', 50);
            $table->string('Descripcion', 100)->nullable();
            $table->integer('OrigenID');
            $table->integer('SubOrigenID');
            $table->integer('Activo')->default(1);



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
        Schema::dropIfExists('FLU_OrigenFuente');
    }
};
