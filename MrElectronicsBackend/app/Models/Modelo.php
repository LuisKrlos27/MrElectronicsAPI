<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    protected $table = 'modelos';

    protected $fillable = ['marca_id', 'nombre'];

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'modelo_id');
    }
}
