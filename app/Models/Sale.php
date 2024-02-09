<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $connection = 'mysql-pompeyo';

    protected $table = 'VT_Ventas';

    protected $primaryKey = 'ID';

    protected $dates = ['FechaVenta'];

    public function inscription()
    {
        return $this->hasOne( Inscription::class, 'VentaID', 'ID' );
    }

    public function client()
    {
        return $this->belongsTo( Client::class, 'ClienteID', 'ID' );
    }

    public function brand()
    {
        return $this->belongsTo( Brand::class, 'MarcaID', 'ID' );
    }

    public function carModel()
    {
        return $this->belongsTo( CarModel::class, 'ModeloID', 'ID' );
    }

    public function scopeFilters(Builder $query, array $filters)
    {
        $query->when($filters['desde'], function ($query, $from) {
            $query->where('FechaVenta', '>=', $from);
        })->when($filters['hasta'], function ($query, $until) {
            $query->where('FechaVenta', '<=', $until);
        });
    }
}
