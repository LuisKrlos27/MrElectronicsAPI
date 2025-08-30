<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proceso extends Model
{
    protected $table = 'procesos';

    protected $fillable = [
        'cliente_id',
        'marca_id',
        'modelo_id',
        'pulgada_id',
        'falla',
        'descripcion',
        'estado',
        'fecha_inicio',
        'fecha_cierre',
    ];

    protected $casts = [
        'estado'       => 'boolean',
        'fecha_inicio' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    // Relación con Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    // Relación con Marca
    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    // Relación con Modelo
    public function modelo()
    {
        return $this->belongsTo(Modelo::class, 'modelo_id');
    }

    public function pulgada()
    {
        return $this->belongsTo(Pulgada::class, 'pulgada_id');
    }


    // Relación con Evidencias
    public function evidencias()
    {
        return $this->hasMany(Evidencia::class, 'proceso_id');
    }
}
