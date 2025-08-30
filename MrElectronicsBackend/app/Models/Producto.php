<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'tipo_id',
        'marca_id',
        'modelo_id',
        'pulgada_id',
        'precio',
        'cantidad',
        'numero_pieza',
        'descripcion'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'cantidad' => 'integer',
    ];

    // Relaciones
    public function tipo()
    {
        return $this->belongsTo(Tipo::class, 'tipo_id');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class, 'modelo_id');
    }

    public function pulgada()
    {
        return $this->belongsTo(Pulgada::class, 'pulgada_id');
    }

    public function procesos()
    {
        return $this->hasMany(Proceso::class, 'producto_id');
    }

    public function ventas()
    {
        return $this->belongsToMany(Venta::class, 'detalle_ventas', 'producto_id', 'venta_id')
            ->withPivot(['cantidad', 'precio_unitario', 'subtotal']);
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'producto_id');
    }
}
