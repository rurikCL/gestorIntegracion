<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'MK_Leads';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [ 'ID', 'FechaCreacion', 'OrigenID', 'SubOrigenID', 'ClienteID', 'SucursalID', 'VendedorID', 'MarcaID', 'ModeloID', 'VersionID', 'EstadoID', 'SubEstadoID', 'Financiamiento', 'CampanaID', 'IntegracionID', 'IDExterno', 'ConcatID', 'Asignado', 'Llamado', 'Agendado', 'Venta', 'ReferenciaID', 'Cotizado', 'Vendido', 'FechaReAsignado', 'Comentario', 'Contesta' ];

    public function user()
    {
        return $this->belongsTo( Client::class, 'ClienteID', 'ID' );
    }

    public function branchOffice()
    {
        return $this->belongsTo( BranchOffice::class, 'SucursalID', 'ID' );
    }

    public function origin()
    {
        return $this->belongsTo( OriginLead::class, 'OrigenID', 'ID' );
    }
}
