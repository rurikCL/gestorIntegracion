<?php

namespace App\Models\MA;

use App\Models\VT\VT_Ventas;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function numVentas() : Attribute
    {
        return Attribute::make(
            get: fn () => $this->ventas->where('EstadoVentaID', 4)->count()
        );
    }

    public function getRutFormatAttribute()
    {
        return substr($this->Rut, 0, strlen($this->Rut) - 1) . '-' . substr($this->Rut, -1);
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
}
