<?php

namespace App\Models\MA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_Origenes extends Model
{
    use HasFactory;
    protected $table = 'MA_Origenes';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'Origen',
        'ActivoInput',
        'Visible',
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
    ];
    public function subOrigen()
    {
        return $this->hasMany(MA_SubOrigenes::class, 'OrigenID', 'ID');
    }
}
