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
        Schema::create('FLU_Monitor', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('FlujoID')->unsigned();
            $table->string('Accion')->nullable();
            $table->string('Estado')->nullable();
            $table->string('Mensaje')->nullable();
            $table->dateTime('FechaInicio')->nullable();
            $table->dateTime('FechaTermino')->nullable();
            $table->integer('Duracion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('FLU_Monitor');
    }
};
