<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    protected $fillable = [
        "tipo_activo_id",
        "modulo",
        "tipo_falla",
        "severidad",
        "prueba",
        "resultado",
        "bug",
        "estado",
        "user_id",
        "fecha",
        "hora",
    ];

    public function tipo_activo()
    {
        return $this->belongsTo(TipoActivo::class, 'tipo_activo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
