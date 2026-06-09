<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EjecucionTrazabilidad extends Model
{
    protected $fillable = [
        "activo_id",
        "estado",
        "trazabilidad",
        "user_id",
        "fecha",
        "hora",
    ];

    public function activo()
    {
        return $this->belongsTo(Activo::class, 'activo_id');
    }

    public function ejecucion_archivos()
    {
        return $this->hasMany(EjecucionArchivo::class, 'ejecucion_trazabilidad_id');
    }
}
