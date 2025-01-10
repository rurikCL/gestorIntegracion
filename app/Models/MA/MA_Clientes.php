<?php

namespace App\Models\MA;

use App\Models\CC\CC_Reclamos;
use App\Models\MK\MK_Leads;
use App\Models\SIS\SIS_Agendamientos;
use App\Models\SIS\SIS_Seguimientos;
use App\Models\SIS\SIS_Solicitudes;
use App\Models\VT\VT_ClientesDiarios;
use App\Models\VT\VT_Cotizaciones;
use App\Models\VT\VT_Renovaciones;
use App\Models\VT\VT_Ventas;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Termwind\Components\Raw;

class MA_Clientes extends Model
{
    use HasFactory;

    protected $table = 'MA_Clientes';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'Nombre',
        'Rut',
        'Email',
        'Telefono',
        'Direccion',
        'Telefono',
        'Telefono2',
        'Telefono3',
        'Telefono4',
        'FechaNacimiento',
        'Direccion',
        'ComunaID',
        'RegionID',
        'NacionalidadID',
        'EstadoCivilID',
        'ProfesionID',
        'AntiguedadLaboral',
        'TipoTrabajadorID',
        'TipoContratoID',
        'TipoRemuneracionID',
        'TipoClienteID',
        'InteresesID',
        'UltimaVentaID',
        'Empleador',
        'SueldoLiquido',
        'Patrimonio',
        'OtrosIngresos',
        'Genero',
        'IngresoMesUno',
        'IngresoMesDos',
        'IngresoMesTres',
        'IngresoMesCuatro',
        'IngresoMesCinco',
        'IngresoMesSeis',
        'IngresoMesSiete',
        'IngresoMesOcho',
        'IngresoMesNueve',
        'IngresoMesDiez',
        'IngresoMesOnce',
        'IngresoMesDoce',
        'SegundoNombre',
        'Apellido',
        'SegundoApellido',
        'ComoConocioPompeyo',
        'TipoJuridicoID',
        'NombreContacto',
        'CargoContacto',
        'Calificacion'
    ];

    public function getRutFormatAttribute()
    {
        return substr($this->Rut, 0, strlen($this->Rut) - 1) . '-' . substr($this->Rut, -1);
    }

    public function getRutValidoAttribute()
    {
        return DB::select("select IF('".$this->Rut."' REGEXP('^[0-9]{8,9}[0-9kK]{1}$'), IF(validate_rut('".$this->Rut."'), 'Si', 'No'), 'No') as RutValido")[0]->RutValido;
    }


    public function region()
    {
        return $this->hasOne(MA_Regiones::class, 'ID', 'RegionID');
    }

    public function comuna()
    {
        return $this->hasOne(MA_Comunas::class, 'ID', 'ComunaID');
    }

    public function ventas()
    {
        return $this->hasMany(VT_Ventas::class, 'ClienteID', 'ID');
    }

    public function leads()
    {
        return $this->hasMany(MK_Leads::class, 'ClienteID', 'ID');
    }

    public function cotizaciones(){
        return $this->hasMany(VT_Cotizaciones::class, 'ClienteID', 'ID');
    }

    public function seguimientos(){
        return $this->hasMany(SIS_Seguimientos::class, 'ClienteID', 'ID');
    }

    public function diarios(){
        return $this->hasMany(VT_ClientesDiarios::class, 'ClienteID', 'ID');
    }

    public function solicitudes(){
        return $this->hasMany(SIS_Solicitudes::class, 'ClienteID', 'ID');
    }

    public function agendamientos(){
        return $this->hasMany(SIS_Agendamientos::class, 'ClienteID', 'ID');
    }

    public function renovaciones(){
        return $this->hasMany(VT_Renovaciones::class, 'ClienteID', 'ID');
    }

    public function reclamos(){
        return $this->hasMany(CC_Reclamos::class, 'ClienteID', 'ID');
    }

    public function validacion($query)
    {
        $query->join(DB::selectRaw("select ".$query->ID." as ID, IF(".$query->Rut." REGEXP('^[0-9]{8,9}[0-9kK]{1}$'), validate_rut(".$query->Rut."), 'No') as RutValido from MA_Clientes v") , 'MA_Clientes.ID', '=', 'v.ID');
    }

    public function clientesDuplicados(){
        return $this->hasMany(MA_Clientes::class, 'Rut', 'Rut')
            ->where('ID' ,'<>', $this->ID);
    }

    public function scopeDuplicados($query, $threshold = 1)
    {
        return $query->groupBy('Rut')->havingRaw('COUNT(*) > '. $threshold)->groupBy('Rut');
    }
}
