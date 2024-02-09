<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    private $table = "API_Proveedores";
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
            $table->integer('ApiID');
            $table->string('Nombre');
            $table->string('Tipo');
            $table->text('Url');
            $table->text('Header');
            $table->string('Metodo');
            $table->string('TipoEntrada');
            $table->string('Params')->nullable();
            $table->mediumText('Json')->nullable();
            $table->string('TipoRespuesta');
            $table->string('IndiceError')->nullable();
            $table->string('IndiceExito')->nullable();
            $table->string('IndiceRespuesta')->nullable();
            $table->string('IndiceExpiracion')->nullable();
            $table->integer('TiempoExpiracion')->default(0);
            $table->integer('Timeout')->default(0);
            $table->string('Token')->nullable();
            $table->string('User')->nullable();
            $table->string('Password')->nullable();

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
