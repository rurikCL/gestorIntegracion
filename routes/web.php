<?php

use App\Http\Controllers\Api\ApiSolicitudController;
use App\Http\Livewire\ListUser;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group( function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/users', ListUser::class)->name('list-user');
    Route::get('/test', [ApiSolicitudController::class, 'index']);

});
Route::get('/', function () {
    return redirect('/admin');
});


Route::get('/flujo/kia/', [\App\Http\Controllers\Flujo\FlujoController::class, 'sendOtIndumotora']);
Route::get('/flujo/kiaventas/', [\App\Http\Controllers\Flujo\FlujoController::class, 'sendVentasIndumotora']);
Route::get('/flujo/kiaOTs/', [\App\Http\Controllers\Flujo\FlujoController::class, 'sendOTsSICIndumotora']);
Route::get('/flujo/mg/', [\App\Http\Controllers\Flujo\FlujoController::class, 'sendLeadMG']);
Route::get('/flujo/mgc/', [\App\Http\Controllers\Flujo\FlujoController::class, 'sendCotizacionMG']);
Route::get('/flujo/movicenter/', [\App\Http\Controllers\ApiProd\FLujoHomologacionController::class, 'insertaMasivo']);
Route::get('/flujo/apcStock/', [\App\Http\Controllers\Flujo\FlujoController::class, 'actualizaStockAPC']);
Route::get('/flujo/apcNV/', [\App\Http\Controllers\Flujo\FlujoController::class, 'notaVentaAPC']);
Route::get('/flujo/apcHomo/', [\App\Http\Controllers\ApcDmsController::class, 'homologacionAPC']);
Route::get('/flujo/apcHomoBancos/', [\App\Http\Controllers\ApcDmsController::class, 'getBancos']);
Route::get('/flujo/cpd/', [\App\Http\Controllers\Flujo\FlujoController::class, 'sendCpdVentas']);
Route::get('/flujo/inchcapeventas/', [\App\Http\Controllers\Flujo\FlujoController::class, 'sendVentasInchcape']);
Route::get('/flujo/inchcapeots/', [\App\Http\Controllers\Flujo\FlujoController::class, 'sendOTsinchcape']);


Route::get('/flujo/autored/transacciones', [\App\Http\Controllers\Flujo\FlujoController::class, 'autoredTransactions']);
Route::get('/flujo/autored/inspecciones', [\App\Http\Controllers\Flujo\FlujoController::class, 'autoredInspections']);

Route::get('/flujo/gema/', [\App\Http\Controllers\Flujo\FlujoController::class, 'leadsGema']);


Route::get('/flujo/santander/modelos', [\App\Http\Controllers\Api\FinancierasController::class, 'homologacionModelos']);
Route::get('/flujo/santander/simulacion', [\App\Http\Controllers\Api\FinancierasController::class, 'simulacionSantander']);

Route::get('/flujo/hubspot/contactos', [\App\Http\Controllers\Flujo\FlujoController::class, 'leadsHubspot']);
Route::get('/flujo/hubspot/negocios', [\App\Http\Controllers\Flujo\FlujoHubspotController::class, 'leadsHubspotDeals']);
Route::get('/flujo/hubspot/actualizanegocios', [\App\Http\Controllers\Flujo\FlujoHubspotController::class, 'actualizaLeadHubspot']);
Route::get('/flujo/hubspot/revisanegocios', [\App\Http\Controllers\Flujo\FlujoHubspotController::class, 'revisaLeadsHubspot']);
Route::get('/flujo/hubspot/sincroniza', [\App\Http\Controllers\Flujo\FlujoHubspotController::class, 'sincronizaLeads']);


Route::get('/flujo/indicador/uf', [\App\Http\Controllers\Flujo\FlujoController::class, 'cargaIndicadoresUF']);
Route::get('/flujo/indicador/utm', [\App\Http\Controllers\Flujo\FlujoController::class, 'cargaIndicadoresUTM']);
Route::get('/flujo/indicador/dolar', [\App\Http\Controllers\Flujo\FlujoController::class, 'cargaIndicadoresDolar']);

//Route::get('posts', [PostController::class, 'index']);
Route::get('/email', [\App\Http\Controllers\EmailController::class, 'sendEmail']);


Route::get('/robot/apc/login', [\App\Http\Controllers\RobotApcController::class, 'login']);
Route::get('/robot/apc/stock', [\App\Http\Controllers\RobotApcController::class, 'traeStock']);
Route::get('/robot/apc/stockAnual', [\App\Http\Controllers\RobotApcController::class, 'traeStockAnual']);
Route::get('/robot/apc/sku', [\App\Http\Controllers\RobotApcController::class, 'traeSku']);
Route::get('/robot/apc/repuestos', [\App\Http\Controllers\RobotApcController::class, 'traeRepuestos']);
Route::get('/robot/apc/ventas', [\App\Http\Controllers\RobotApcController::class, 'traeMovimientosVentas']);
Route::get('/robot/apc/rentabilidadOt', [\App\Http\Controllers\RobotApcController::class, 'traeRentabilidadOt']);
Route::get('/robot/apc/rentabilidadSku', [\App\Http\Controllers\RobotApcController::class, 'traeRentabilidadSku']);
Route::get('/robot/apc/informeOt', [\App\Http\Controllers\RobotApcController::class, 'traeInformeOt']);
Route::get('/robot/apc/rentabilidadVenta', [\App\Http\Controllers\RobotApcController::class, 'traeRentabilidadVenta']);


require __DIR__.'/auth.php';
