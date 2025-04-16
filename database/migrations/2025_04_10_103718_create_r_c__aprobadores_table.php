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
        Schema::create('r_c__aprobadores', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('UserID')->unsigned();
            $table->bigInteger('SucursalID')->unsigned();
            $table->integer('Nivel')->default(1);
            $table->integer('Estado')->default(1); // 0: inactivo, 1: activo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('r_c__aprobadores');
    }
};
