<?php

namespace App\Http\Controllers;

use App\Models\FLU_Monitor;
use Illuminate\Http\Request;

class MonitorFlujo extends Controller
{

    private $id;
    private $FlujoID;
    public function __construct($FlujoID)
    {
        $this->FlujoID = $FlujoID;
    }

    public function registrarInicio($accion, $estado = 'INICIO')
    {
        $monitor = new FLU_Monitor();
        $monitor->FlujoID = $this->FlujoID;
        $monitor->Accion = $accion;
        $monitor->Estado = $estado;
        $monitor->FechaInicio = date("Y-m-d H:i:s");
        $monitor->save();

        $this->id = $monitor->id;
        return $monitor->id;
    }


    public function registrarFin($accion, $estado = 'TERMINADO'){
        $monitor = FLU_Monitor::where('id', $this->id)->first();
        $monitor->Estado = $estado;
        $monitor->FechaTermino = date("Y-m-d H:i:s");
        $monitor->Duracion = strtotime($monitor->FechaTermino) - strtotime($monitor->FechaInicio);
        $monitor->save();
    }
}
