<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockCollection;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        return new StockCollection(
            Stock::select('ID', 'Marca', 'Modelo', 'Version', 'Anno', 'ColorExterior', 'VIN', 'DisponibleENissan', 'PrecioVenta', 'Sucursal')
                ->where('DisponibleENissan','>', 0)
                ->paginate()
        );
    }

    public function update(Request $request)
    {
        $estado = $request->input('data.estado');

        if ($request->input('data.vin')) {
            $stock = Stock::where('Vin', $request->input('data.vin'))->first();

        } else if ($request->input('data.id')) {
            $stock = Stock::where('id', $request->input('data.id'))->first();

        } else {
            return response()->json(["data" => [
                "success" => false,
                "message" => "No se ha definido el ID o VIN del vehiculo "
            ]], 404);
        }

        if ($stock) {

            $stock->DisponibleENissan = $estado;
            $stock->save();

            return response()->json(["data" => [
                "success" => true,
                "message" => "Estado de stock actualizado correctamente"
            ]], 200);

        } else {
            return response()->json(["data" => [
                "success" => false,
                "message" => "Ocurrio un error al actualizar estado de stock "
            ]], 404);
        }
    }
}
