<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Services\PermisoService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "usuario",
        "nombre",
        "apellido",
        "email",
        "fono",
        "password",
        "foto",
        "acceso",
        "tipo",
        "fecha_registro",
        "status",
    ];

    protected $appends = ["permisos", "url_foto", "foto_b64", "full_name", "full_ci", "fecha_registro_t", "usuario_abrev"];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }


    public function getUsuarioAbrevAttribute()
    {
        $tam_usuario = strlen($this->usuario);
        if ($tam_usuario > 8) {
            return substr($this->usuario, 0, 8) . "...";
        }

        return $this->usuario;
    }

    public function getPermisosAttribute()
    {
        $permisoService = new PermisoService();
        return $permisoService->getPermisosUser();
    }

    public function getFechaRegistroTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_registro));
    }

    public function getFullNameAttribute()
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function getFullCiAttribute()
    {
        return $this->ci . ' ' . $this->ci_exp;
    }

    public function getUrlFotoAttribute()
    {
        if ($this->foto) {
            return asset("imgs/users/" . $this->foto);
        }
        return asset("imgs/users/default.png");
    }

    public function getFotoB64Attribute()
    {
        $path = public_path("imgs/users/" . $this->foto);
        if (!$this->foto || !file_exists($path)) {
            $path = public_path("imgs/users/default.png");
        }
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public function scopeBuscarNombre($query, $texto)
    {
        if (!$texto) return $query;

        $palabras = explode(' ', $texto);

        foreach ($palabras as $palabra) {
            $query->where(function ($q) use ($palabra) {
                $q->where('nombre', 'like', "%$palabra%")
                    ->orWhere('paterno', 'like', "%$palabra%")
                    ->orWhere('materno', 'like', "%$palabra%")
                    ->orWhere('usuario', 'like', "%$palabra%")
                    ->orWhere('tipo', 'like', "%$palabra%")
                    ->orWhere('ci', 'like', "%$palabra%");
            });
        }

        return $query;
    }
}
