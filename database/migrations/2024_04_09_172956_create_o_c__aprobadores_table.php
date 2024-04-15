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
        Schema::create('OC_Aprobadores', function (Blueprint $table) {
            $table->id('ID');
            $table->unsignedBigInteger('SucursalID');
            $table->integer('Nivel');
            $table->unsignedBigInteger('UserID');
            $table->integer('Min');
            $table->integer('Max');
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
        Schema::dropIfExists('OC_Aprobadores');
    }
};
