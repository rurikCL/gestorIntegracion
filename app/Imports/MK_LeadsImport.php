<?php

namespace App\Imports;

use App\Http\Controllers\Api\LeadController;
use App\Models\MA\MA_Clientes;
use App\Models\MK\MK_Leads;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;

class MK_LeadsImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts, ShouldQueue, WithEvents
{
    private $carga = null;
    private $marca = null;



    public function __construct($carga, $marca)
    {
        $this->carga = $carga;
        $this->marca = $marca;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $collection)
    {
        Log::info("Inicio de revision de Leads");

        // Logica para importar cotizaciones
        $contadorRegistro = 0;
        $errores = [];
        $data = [];
        $leadController = new LeadController();
        $registrosOK = 0;
        $registrosFallidos = 0;
        $registrosTotales = 0;

        foreach ($collection as $record) {
            $data['id'] = $record["id"];
            $data['created'] = $record["created_time"];
            $data['sucursal'] = $record["sucursal"];
            $data['nombre'] = $record["full_name"];
            $data['rut'] = $record["rut"];
            $data['telefono'] = $record["phone_number"];
            $data['email'] = $record["email"];
            $data['marca'] = $this->marca;
            $data['modelo'] = $record["modelo"] ?? '';

            $crearLead = false;

            $lead = MK_Leads::where('OrigenID', 8)
                ->where('SubOrigenID', 36)
                ->where('IDExterno', $data['id'])
//                ->where('FechaCreacion', Carbon::createFromFormat('d-m-Y',$data['created'])->format("Y-m-d"))
                ->first();

            if (!$lead) {
                Log::info("Lead no existe, busqueda de datos: ");

                // Se busca si el cliente existe
                $cliente = MA_Clientes::where('Rut', $data['rut'])->first();
                if ($cliente) {
                    $lead = MK_Leads::where('ClienteID', $cliente->ID)
                        ->where('OrigenID', 8)
                        ->where('SubOrigenID', 36)
                        ->first();

                    if ($lead) {
                        Log::info("Lead ya existe: " . $lead->id);
                        $lead->IDExterno = $data['id'];
                        $lead->save();
                    } else {
                        Log::info("Lead no existe, creacion: ");
                        $crearLead = true;
                    }

                } else {
                    Log::info("Cliente no existe, Lead no existe, creacion: PENDIENTE ");
                    $crearLead = true;

                }

                if ($crearLead) {
                    $request = new \Illuminate\Http\Request();
                    $request->replace(
                        array(
                            'data' =>
                                array(
                                    'usuarioID' => 2826,
                                    'reglaVendedor' => true,
                                    'reglaSucursal' => false,
                                    'nombre' => $data['nombre'],
                                    'rut' => $data['rut'],
                                    'email' => $data['email'],
                                    'telefono' => $data['telefono'],
                                    'lead' =>
                                        array(
                                            'idFlujo' => 8,
                                            'origenID' => 8,
                                            'subOrigenID' => 36,
                                            'sucursal' => $data['sucursal'],
                                            'marca' => $data['marca'],
                                            'modelo' => $data['modelo'],
                                            'externalID' => $data['id'],
                                            'comentario' => '',
                                        )
                                )
                        )
                    );
                    $nuevoLead = $leadController->NuevoLead($request);
                    $registrosFallidos++;
                }

            } else {
                $registrosOK++;
            }
            $registrosTotales++;
        }

        $this->carga->fresh();
        $this->carga->RegistrosCargados = $this->carga->RegistrosCargados + $registrosOK;
        $this->carga->RegistrosFallidos = $this->carga->RegistrosFallidos + $registrosFallidos;
        $this->carga->save();

        Log::info("Fin de revision de Leads");
    }

    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            AfterImport::class => function (AfterImport $event) {

                $totalRows = $event->getReader()->getTotalRows();
                $this->carga->Registros = $totalRows['Worksheet'];
                $this->carga->Estado = 2;
                $this->carga->save();
            },

        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 100;
    }
}
