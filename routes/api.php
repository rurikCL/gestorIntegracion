<?php

use App\Http\Controllers\Api\ApiSolicitudController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchOfficeController;
use App\Http\Controllers\Api\CarModelController;
use App\Http\Controllers\Api\DiaryController;
use App\Http\Controllers\Api\FinancierasController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\OriginLeadController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\SubOriginLeadController;
use App\Http\Controllers\ApiProd\ClientesController;
use App\Http\Controllers\ApiProd\GerenciasController;
use App\Http\Controllers\ApiProd\MarcasController;
use App\Http\Controllers\ApiProd\ModelosController;
use App\Http\Controllers\ApiProd\SisAgendamientosController;
use App\Http\Controllers\ApiProd\SucursalesController;
use App\Http\Controllers\ApiProd\TicketController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ AuthController::class, 'login'])->name( 'api.login' );

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/leads', [LeadController::class, 'index']);
    Route::post('/lead', [LeadController::class, 'store']);
    Route::get('/lead/{lead}', [LeadController::class, 'show']);

    Route::get('/agenda', [ DiaryController::class, 'index' ])->name('diary');

    Route::get('/origen-lead', [ OriginLeadController::class, 'index' ])->name('origin-lead');
    Route::get('/sub-origen-lead', [ SubOriginLeadController::class, 'index' ])->name('sub-origin-lead');
    Route::get('/sucursales', [ BranchOfficeController::class, 'index' ])->name('branch-office');
    Route::get('/modelos', [ CarModelController ::class, 'index' ])->name('car-model');

    Route::get('/stock-nissan', [StockController::class, 'index'])->name('stock-nissan');
    Route::post('/stock-nissan-update', [StockController::class, 'update'])->name('stock-nissan-update');

    Route::get('/solicitudes', [ApiSolicitudController::class, 'index']);
    Route::post('/nuevasol', [ApiSolicitudController::class, 'store']);
    Route::get('/infosol/{referenciaID}', [ApiSolicitudController::class, 'show']);
    Route::get('/infoFinancieras', [ApiSolicitudController::class, 'getSolicitudesFlujo']);


    Route::get('/get/clientes', [ClientesController::class, 'index']);
    Route::get('/get/cliente/{rut}', [ClientesController::class, 'show']);
    Route::post('/update/cliente', [ClientesController::class, 'update']);
    Route::get('/get/marcas', [GerenciasController::class, 'index']);
    Route::get('/get/modelos', [ModelosController::class, 'index']);
    Route::get('/get/sucursales', [SucursalesController::class, 'index']);
    Route::post('/set/lead', [LeadController::class, 'nuevoLead']);
    Route::get('/get/infocliente', [ClientesController::class, 'infoClienteVenta']);
    Route::get('/get/inforeclamos', [ClientesController::class, 'infoReclamos']);
    Route::post('/set/ticket', [TicketController::class, 'store']);
    Route::post('/set/optiman', [SisAgendamientosController::class, 'store']);


    Route::post('/financieras/enviar', [FinancierasController::class, 'enviarFinancieras']);
    Route::post('/santander/calculadora', [FinancierasController::class, 'calculadoraSantander']);
    Route::post('/santander/solicitud', [FinancierasController::class, 'creditoSantander']);

});
