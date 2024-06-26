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
        $idUsuario = $request->input('idUsuario');
        $ip = $request->input('ip');
        $comentario = $request->input('comentario');

        $evento = SIS_Eventos::create(
            [
                'FechaCreacion' => Carbon::now("Y-m-d H:i:s"),
                'Comentario' => $ip,
                'UsuarioCreacionID' => $idUsuario,
                'ReferenciaID' => 0,
                'MenuSecundarioID' => 0,
                'EventoCreacionID' => 0
            ]
        );

        return 'OK';
    }
}
