<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SaleCollection;
use App\Models\Inscription;
use App\Models\Sale;
use Illuminate\Http\Request;

class DiaryController extends Controller
{
    /**
     * Muestra los registros de Incripciones para agendamiento
     * @OA\Get(
     *     path="/api/agenda",
     *     tags={"Agenda"},
     *     summary="Mostrar Incripciones para agendamiento",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar todos las inscripciones."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function index()
    {

        $sales = Sale::whereHas(  'inscription'/*, function ( $query) {
            return $query->whereNotNull( 'Patente')
                ->where('Patente', '<>', "")
                ->where('EtapaID', '<>', 5);
            }*/)
            ->where( 'EstadoVentaID', 4 )
            ->whereNotNull('Vin')
            ->whereNotNull('Patente')
            ->whereYear('FechaFactura', '>', '2019')
            ->select('ID', 'FechaVenta', 'ClienteID', 'MarcaID', 'ModeloID', 'Vin', 'Patente')
            ->filters(["desde" => \request('filter.desde'), "hasta" => \request('filter.hasta')])
            ->paginate();

        /*$query = Author::query();

        $query->when(request('filter_by') == 'likes', function ($q) {
            return $q->where('likes', '>', request('likes_amount', 0));
        });
        $query->when(request('filter_by') == 'date', function ($q) {
            return $q->orderBy('created_at', request('ordering_rule', 'desc'));
        });*/

        // $authors = $query->get();

        return SaleCollection::make( $sales );
    }
}
