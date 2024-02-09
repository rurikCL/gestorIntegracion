<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    private $table = 'API_Autenticaciones';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            $table->dateTime('FechaCreacion');
            $table->integer('EventoCreacionID');
            $table->integer('UsuarioCreacionID');
            $table->dateTime('FechaActualizacion');
            $table->integer('EventoActualizacionID');
            $table->integer('UsuarioActualizacionID');

            $table->integer('ProveedorID');
            $table->text('Token1')->nullable();
            $table->text('Token2')->nullable();
            $table->integer('Expiration')->default(0);
            $table->dateTime('FechaInicio')->nullable();
            $table->dateTime('FechaExpiracion')->nullable();
            $table->string('Status')->nullable();
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
        Schema::dropIfExists($this->table);
    }
};
