<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pulgada extends Model
{
    protected $table = 'pulgadas';

    protected $fillable = ['medida'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'pulgada_id');
    }
}
