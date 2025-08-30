<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
    protected $table = 'evidencias';

    protected $fillable = ['proceso_id', 'imagen', 'comentario'];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }
}
