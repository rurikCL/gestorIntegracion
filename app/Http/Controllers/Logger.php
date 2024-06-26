<?php

namespace App\Http\Controllers;

use App\Models\Api\API_LogSolicitud;
use App\Models\SIS_Eventos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Logger extends Controller
{
    //
    private $logArray = array();
    private $mustLog = true;

    public function info($message, $idSolicitud = null, array $context = array()) :bool
    {
        if($this->mustLog) Log::info($message, $context);

        if($idSolicitud != null) {
            API_LogSolicitud::create([
                'SolicitudID' => $idSolicitud,
                'Tipo' => 'info',
                'Mensaje' => $message,
                'Fecha' => date('Y-m-d H:i:s'),
                'UsuarioID'=> 1,
            ]);

        } else {
            $this->logArray[] = array('info', $message, date('Y-m-d H:i:s'));
        }
        return true;
    }
    public function notice($message, $idSolicitud = null, array $context = array()) :bool
    {
        if($this->mustLog) Log::notice($message, $context);

        if($idSolicitud != null) {
            API_LogSolicitud::create([
                'SolicitudID' => $idSolicitud,
                'Tipo' => 'notice',
                'Mensaje' => $message,
                'Fecha' => date('Y-m-d H:i:s'),
                'UsuarioID'=> 1,
            ]);

        } else {
            $this->logArray[] = array('info', $message, date('Y-m-d H:i:s'));
        }
        return true;
    }

    public function warning($message, $idSolicitud = null, array $context = array()) :bool
    {
        if($this->mustLog) Log::warning($message, $context);

        if($idSolicitud != null) {
            API_LogSolicitud::create([
                'SolicitudID' => $idSolicitud,
                'Tipo' => 'warning',
                'Mensaje' => $message,
                'Fecha' => date('Y-m-d H:i:s'),
                'UsuarioID'=> 1,
            ]);

        } else {
            $this->logArray[] = array('error', $message, date('Y-m-d H:i:s'));
        }
        return true;
    }
    public function error($message, $idSolicitud = null, array $context = array()) :bool
    {
        if($this->mustLog) Log::info($message, $context);
        if($idSolicitud != null) {
            API_LogSolicitud::create([
                'SolicitudID' => $idSolicitud,
                'Tipo' => 'error',
                'Mensaje' => $message,
                'Fecha' => date('Y-m-d H:i:s'),
                'UsuarioID'=> 1,
            ]);

        } else {
            $this->logArray[] = array('error', $message, date('Y-m-d H:i:s'));
        }
        return true;
    }

    public function solveArray($idSolicitud)
    {
        if($this->mustLog) Log::info('Logger : Solving array of logs for solicitud: ' . $idSolicitud . ' with ' . count($this->logArray) . ' logs');

        if($idSolicitud != null) {

            foreach ($this->logArray as $log) {
                API_LogSolicitud::create([
                    'SolicitudID' => $idSolicitud,
                    'Tipo' => $log[0],
                    'Mensaje' => $log[1],
                    'Fecha' => $log[2],
                    'UsuarioID'=> 1,
                ]);
            }
            $this->logArray = array();
        }
    }

    public function getLogArray()
    {
        return $this->logArray;
    }

    public function setMustLog($mustLog)
    {
        $this->mustLog = $mustLog;
    }

    public function logEvento(Request $request)
    {

        $idUsuario = $request->input('data.idUsuario');
        $ip = $request->input('data.ip');
        $comentario = $request->input('data.comentario');
        $fecha = $request->input('data.fecha');
        $referencia = $request->input('data.referencia') ?? 0;
        $menuSecundario = $request->input('data.menuSecundario') ?? 11;
        $evento = $request->input('data.evento') ?? 125;

        $evento = SIS_Eventos::create(
            [
                'FechaCreacion' => $fecha ?? Carbon::now(),
                'Ip' => $ip,
                'Comentario' => $comentario,
                'UsuarioCreacionID' => $idUsuario,
                'ReferenciaID' => $referencia,
                'MenuSecundarioID' => $menuSecundario,
                'EventoCreacionID' => $evento
            ]
        );

        if($evento)
          return response()->json(['messages' => 'Log creado'], 200);
        else
          return response()->json(['messages' => 'Error al crear log'], 500);

    }
}
