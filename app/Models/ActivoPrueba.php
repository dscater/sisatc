<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivoPrueba extends Model
{
    protected $fillable = [
        "activo_id",
        "descripcion",
        "modulo",
        "prueba",
        "user_id",
        "fecha",
        "hora",
    ];

    protected $appends = ["fecha_t"];

    public function getFechaTAttribute()
    {
        return date("d-m-Y", strtotime($this->fecha));
    }

    public function activo()
    {
        return $this->belongsTo(Activo::class, 'activo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
