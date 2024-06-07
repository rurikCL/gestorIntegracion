<?php

namespace App\Models\MA;

use App\Models\MK\MK_Leads;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_SubOrigenes extends Model
{
    use HasFactory;

    protected $table = 'MA_SubOrigenes';
    protected $connection = 'mysql-pompeyo';


    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'OrigenID',
        'SubOrigen',
        'ActivoInput',
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'Alias'
    ];

    public function origen()
    {
        return $this->belongsTo(MA_Origenes::class, 'OrigenID', 'ID');
    }

    public function scopeAlias($query, $alias)
    {
        return $query->where('Alias', $alias);
    }

    public function lead()
    {
        return $this->belongsTo(MK_Leads::class, 'ID', 'SubOrigenID');
    }

}
