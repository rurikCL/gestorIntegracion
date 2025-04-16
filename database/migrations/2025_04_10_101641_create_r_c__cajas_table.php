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
        Schema::create('RC_Cajas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('sucursal_id')->unsigned();
            $table->bigInteger('total')->nullable();
            $table->text('comentario')->nullable();
            $table->integer('estado')->default(0); // 0: nuevo, 1: aprobado, 2: rechazado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('r_c__cajas');
    }
};
