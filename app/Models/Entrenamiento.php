<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrenamiento extends Model
{
    protected $fillable = [
        "activo",
        "activo_id",
        "modulo",
        "tipo_falla",
        "severidad",
        "prueba",
        "resultado",
        "bug",
        "estado",
        "res",
        "user_id",
        "fecha",
        "hora",
    ];
}
