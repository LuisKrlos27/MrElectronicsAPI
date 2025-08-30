<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';

    // NO incluir 'cambio' en fillable: es columna GENERATED por Postgres
    protected $fillable = ['cliente_id', 'fecha_venta', 'total', 'pago','cambio'];

    protected $casts = [
        'fecha_venta' => 'datetime',
        'total'       => 'decimal:2',
        'pago'        => 'decimal:2',
        'cambio'      => 'decimal:2', // se puede leer; lo calcula la BD
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'detalle_ventas', 'venta_id', 'producto_id')
            ->withPivot(['cantidad', 'precio_unitario', 'subtotal']);
    }
}
