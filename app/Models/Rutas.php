<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rutas extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        ['id' => 1, 'label' => 'Kia', 'descripcion'=>'Flujo de envio de OT a Indumotora (KIA)', 'ruta'=>'/flujo/kia/', 'rutaNombre'=>'sendOtIndumotora'],
        ['id' => 2, 'label' => 'Kia Ventas', 'descripcion'=>'Flujo de envio de Ventas a SIC Indumotora (KIA)', 'ruta'=>'/flujo/kiaventas/', 'rutaNombre'=>'sendVentasIndumotora'],
        ['id' => 3, 'label' => 'Kia OTs', 'descripcion'=>'Flujo de envio de OTs a SIC Indumotora (KIA)', 'ruta'=>'/flujo/kiaOTs/', 'rutaNombre'=>'sendOTsSICIndumotora'],
        ['id' => 4, 'label' => 'MG', 'descripcion'=>'Flujo de envio de Leads MG', 'ruta'=>'/flujo/mg/', 'rutaNombre'=>'sendLeadMG'],
        ['id' => 5, 'label' => 'MG Cotizacion', 'descripcion'=>'Flujo de envio de Cotizaciones MG', 'ruta'=>'/flujo/mgc/', 'rutaNombre'=>'sendCotizacionMG'],
        ['id' => 7, 'label' => 'APC Stock', 'descripcion'=>'Flujo de actualizacion Stock APC', 'ruta'=>'/flujo/apcStock/', 'rutaNombre'=>'actualizaStockAPC'],
//        ['id' => 8, 'label' => 'APC Nota Venta', 'descripcion'=>'', 'ruta'=>'/flujo/apcNV/', 'rutaNombre'=>'notaVentaAPC'],
        ['id' => 9, 'label' => 'APC Homologacion', 'descripcion'=>'Flujo de actualizacion de datos Homologados APC', 'ruta'=>'/flujo/apcHomo/', 'rutaNombre'=>'homologacionAPC'],
        ['id' => 10, 'label' => 'APC Homologacion Bancos', 'descripcion'=>'Flujo de actualizacion de Homologacion de Bancos APC', 'ruta'=>'/flujo/apcHomoBancos/', 'rutaNombre'=>'getBancos'],
        ['id' => 12, 'label' => 'Autored Transacciones', 'descripcion'=>'Flujo de importacion de Transacciones Autored', 'ruta'=>'/flujo/autored/transacciones', 'rutaNombre'=>'autoredTransactions'],
        ['id' => 13, 'label' => 'Autored Inspecciones', 'descripcion'=>'Flujo de importacion de Inspecciones Autored', 'ruta'=>'/flujo/autored/' , 'rutaNombre'=>'autoredInspections'],
//        ['id' => 16, 'label' => 'Santander Simulacion', 'descripcion'=>'', 'ruta'=>'/flujo/santander/simulacion/', 'rutaNombre'=>'simulacionSantander'],
        ['id' => 17, 'label' => 'Hubspot Contactos', 'descripcion'=>'Flujo de importacion de Contactos Hubspot', 'ruta'=>'/flujo/hubspot/contactos/', 'rutaNombre'=>'leadsHubspot'],
        ['id' => 18, 'label' => 'Hubspot Negocios', 'descripcion'=>'Flujo de importacion de Negocios Hubspot', 'ruta'=>'/flujo/hubspot/negocios/', 'rutaNombre'=>'leadsHubspotDeals'],
        ['id' => 19, 'label' => 'Hubspot Actualiza Negocios', 'descripcion'=>'Flujo de Actualizacion de Negocios Hubspot', 'ruta'=>'/flujo/hubspot/actualizanegocios/', 'rutaNombre'=>'actualizaLeadHubspot'],
        ['id' => 20, 'label' => 'Indicador UF', 'descripcion'=>'Actualizacion de indicador UF', 'ruta'=>'/flujo/indicador/uf/', 'rutaNombre'=>'cargaIndicadoresUF'],
        ['id' => 21, 'label' => 'Indicador UTM', 'descripcion'=>'Actualizacion de indicador UTM', 'ruta'=>'/flujo/indicador/utm/', 'rutaNombre'=>'cargaIndicadoresUTM'],
        ['id' => 22, 'label' => 'Email', 'descripcion'=>'Prueba de envio de Email', 'ruta'=>'/email/', 'rutaNombre'=>'sendEmail'],

    ];
}
