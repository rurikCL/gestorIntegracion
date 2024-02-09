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
        Schema::create('API_LogSolicitudes', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('SolicitudID');
            $table->text('Mensaje');
            $table->integer('UsuarioID')->nullable();
            $table->string('Tipo', 50)->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->index('SolicitudID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('API_LogSolicitudes');
    }
};
