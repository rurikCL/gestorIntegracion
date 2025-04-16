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
        Schema::create('RC_Aprobaciones', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('CajaID')->unsigned();
            $table->bigInteger('AprobadorID')->unsigned();
            $table->integer('Nivel')->default(1);
            $table->integer('Estado')->default(0); // 0: pendiente, 1: aprobado, 2: rechazado, 3: revision
            $table->text('Comentario')->nullable();
            $table->boolean('Estado')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('r_c__aprobaciones');
    }
};
