<?php

namespace App\Http\Controllers;

use App\Services\PermisoService;
use Illuminate\Http\Request;

class TipoUsuarioController extends Controller
{
    public function __construct(private PermisoService $permiso_service) {}

    public function listado()
    {
        return response()->JSON($this->permiso_service->getTiposUsuarios());
    }
}
