<?php

namespace App\Http\Controllers;

use App\Mail\MonitorError;
use App\Models\FLU_Monitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MonitorFlujoController extends Controller
{

    private $id;
    private $FlujoID;
    private $accion;

    public function __construct($FlujoID, $accion = "")
    {
        $this->FlujoID = $FlujoID;
        $this->accion = $accion;
    }

    public function registrarInicio($estado = 'INICIO')
    {
        $monitor = new FLU_Monitor();
        $monitor->FlujoID = $this->FlujoID;
        $monitor->Accion = $this->accion;
        $monitor->Estado = $estado;
        $monitor->FechaInicio = date("Y-m-d H:i:s");
        $monitor->save();

        $this->id = $monitor->id;
        return $monitor->id;
    }

    public function setMessage($mensaje){
        $monitor = FLU_Monitor::where('id', $this->id)->first();
        $monitor->Mensaje = $mensaje;
        $monitor->save();
    }

    public function setAction($accion){
        $monitor = FLU_Monitor::where('id', $this->id)->first();
        $monitor->Accion = $accion;
        $monitor->save();
    }


    public function registrarFin($estado = 'TERMINADO'){
        $monitor = FLU_Monitor::where('id', $this->id)->first();
        $monitor->Estado = $estado;
        $monitor->FechaTermino = date("Y-m-d H:i:s");
        $monitor->Duracion = strtotime($monitor->FechaTermino) - strtotime($monitor->FechaInicio);
        $monitor->save();
    }

    public function registrarError($mensaje = "Error"){
        $monitor = FLU_Monitor::where('id', $this->id)->first();
        $monitor->Estado = 'ERROR';
        $monitor->Mensaje = $mensaje;
        $monitor->FechaTermino = date("Y-m-d H:i:s");
        $monitor->Duracion = strtotime($monitor->FechaTermino) - strtotime($monitor->FechaInicio);
        $monitor->save();

        Mail::to('cristian.fuentealba@pompeyo.cl')->cc(['rodrigo.larrain@pompeyo.cl', 'rurik.neologik@gmail.com'])
            ->send(new MonitorError($monitor));
    }
}
