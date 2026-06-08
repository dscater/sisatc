<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activo extends Model
{
    protected $fillable = [
        "codigo",
        "nombre",
        "descripcion",
        "tipo_activo_id",
        "version",
        "user_id",
        "fecha_registro",
    ];

    protected $appends = ["fecha_registro_t"];

    public function getFechaRegistroTAttribute()
    {
        return $this->fecha_registro ? date('d/m/Y', strtotime($this->fecha_registro)) : "";
    }

    public function tipo_activo()
    {
        return $this->belongsTo(TipoActivo::class);
    }
}
