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
        Schema::create('FLU_Notificaciones', function (Blueprint $table) {
            $table->id('ID');

            $table->integer('ID_Flujo');
            $table->string('ID_Ref', 150);
            $table->boolean('Notificado');
            $table->timestamps();

            $table->index(['ID_Flujo', 'ID_Ref']);
            $table->index(['ID_Flujo']);
            $table->index(['ID_Ref']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('FLU_Notificaciones');
    }
};
