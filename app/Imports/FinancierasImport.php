<?php

namespace App\Imports;

use AnourValar\EloquentSerialize\Tests\Models\File;
use App\Models\FLU\FLU_Homologacion;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Usuarios;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FinancierasImport implements ToCollection, WithBatchInserts, WithHeadingRow
{
    private $carga = null;
    private $contadorRegistro = 0;
    private $contErrores = 0;

    use Importable;

    public function __construct($carga)
    {
        $this->carga = $carga;
    }

    public function collection(Collection $collection)
    {
        Log::info("Inicio de importacion de Solicitudes Financiera");

        $h = new FLU_Homologacion();

        $errores = [];

        $this->contadorRegistro = $this->carga->RegistrosCargados ?? 0;
        $contErrores = $this->carga->RegistrosFallidos ?? 0;
        $idCarga = $this->carga->ID;

        // Seccion Preguardado --------------------------------------------------
//        $datosPrevios = VT_Salvin::all();
//        VT_Salvin::query()->delete();

        // Seccion de importacion --------------------------------------------------
        foreach ($collection as $record) {
            if ($this->contadorRegistro > 0) {

                $sucursalID = $h->getDato($record['dealer'], 21, 'sucursal', 0);
                $marcaID = MA_Marcas::where('Marca', $record['marca'])->first()->ID ?? 1;
                $modeloID = $h->getDato($record['modelo'], 21, 'modelo', 0);
                $productoID = $h->getDato($record['producto'], 21, 'producto', 0);
                $estadoID = $h->getDato($record['estado'], 21, 'estado', 0);

                $vendedorID = MA_Usuarios::where('Nombre', $record['vendedor'])->first()->ID ?? 1;
                $ejecutivoID = MA_Usuarios::where('Nombre', $record['ejecutivo'])->first()->ID ?? 0;

                $rut = $record['rut_cliente'];
                $rut = substr($rut, 0, strlen($rut) - 1) . "-" . substr($rut, -1, 1);

                $clienteID = MA_Clientes::where('Rut', $rut)->first()->ID ?? 1;

                if($vendedorID == 0){
                    $errores[] = ["message" => "No se encontro vendedor",
                        "record" => $record];
                    continue;
                }

                if($clienteID == 0){
                    $errores[] = ["message" => "No se encontro cliente",
                        "record" => $record];
                    continue;
                }

                // Preparacion de datos desde CSV ------------------------------------------
                $registro = [
                    'Financiera' => $record['financiera_origen'],
                    'Estado' => $record['estado'],
                    'FechaCotizacion' => $record['fecha_cotizacion'],
                    'Sucursal' => $record['sucursal'],
                    'Vendedor' => $record['vendedor'],
                    'NombreEjecutivo' => $record['nombre_ejecutivo'],
                    'RutCliente' => $record['rut_cliente'],
                    'NombreCliente' => $record['nombre_cliente'],
                    'EmailCliente' => $record['email_cliente'],
                    'TelefonoCliente' => $record['telefono_cliente'],
                    'Marca' => $record['marca'],
                    'Modelo' => $record['modelo'],
                    'Producto' => $record['producto'],
                    'TipoCredito' => $record['tipo_credito'],

                    'NuevoUsado' => $record['nuevo_usado'],
                    'TipoCreditoID' => $record['tipocreditoid'],
                    'SucursalID' => $record['idsucursal'],
                    'EstadoID' => $record['idestado'],
                    'MarcaID' => $record['idmarca'],
                    'ModeloID' => $record['idmodelo'],

                    'VendedorID' => $vendedorID,
                    'EjecutivoID' => $ejecutivoID,
                    'ClienteID' => $clienteID,

                    'Cargado' => 0,
                    'VendedorEnSucursal' => 0,
                    'CanalID' => 0,
                    'OrigenID' => 0,
                    'SubOrigenID' => 0,

                    'Concat' => $record['rut_cliente'] . $sucursalID . $vendedorID . $modeloID,

                    ];
                $registros[] = $registro;

            } // Fin contador registro
            $this->contadorRegistro++;



        } // Fin foreach

        $json = json_encode($registros);
        $errorJson = json_encode($errores);
        Storage::put(storage_path('app/public/santander'.$this->contadorRegistro.'.json'), $json);
        Storage::put(storage_path('app/public/santanderErrors'.$this->contadorRegistro.'.json'), $errorJson);


        // paso final --------------------------------------------------

        $contErrores += count($errores);
        $this->carga->Registros = $contErrores + $this->contadorRegistro;
        $this->carga->RegistrosCargados = $this->contadorRegistro;
        $this->carga->RegistrosFallidos = $contErrores;

        if ($this->contadorRegistro) {
            $this->carga->Estado = 2;
        } else {
            $this->carga->Estado = 3;
        }
        $this->carga->save();

        Log::info("Fin de importacion de Solicitudes Santander");

        return true;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
