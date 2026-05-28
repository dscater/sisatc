<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use App\Models\TipoActivo;
use App\Models\User;
use App\Services\UserService;
use App\Services\PermisoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    public function __construct(private  UserService $userService) {}

    public function permisosUsuario(Request $request)
    {
        $permisoService = new PermisoService();
        return response()->JSON([
            "permisos" => $permisoService->getPermisosUser()
        ]);
    }

    public function getUser()
    {
        return response()->JSON([
            "user" => Auth::user()
        ]);
    }

    public function getInfoBoxUser()
    {
        $permisos = [];
        $array_infos = [];
        if (Auth::check()) {
            $oUser = new User();
            $permisos = $oUser->permisos;
            if ($permisos == '*' || (is_array($permisos) && in_array('usuarios.index', $permisos))) {
                $array_infos[] = [
                    'label' => 'USUARIOS',
                    'cantidad' => User::where('id', '!=', 1)->count(),
                    'color' => 'bgWhite',
                    'icon' => "fa-users",
                    "url" => "usuarios.index"
                ];
            }
            if ($permisos == '*' || (is_array($permisos) && in_array('activos.index', $permisos))) {
                $array_infos[] = [
                    'label' => 'ACTIVOS',
                    'cantidad' => Activo::count(),
                    'color' => 'bgWhite',
                    'icon' => "fa-clipboard-list",
                    "url" => "activos.index"
                ];
            }
            if ($permisos == '*' || (is_array($permisos) && in_array('tipo_activos.index', $permisos))) {
                $array_infos[] = [
                    'label' => 'TIPO DE ACTIVOS',
                    'cantidad' => TipoActivo::count(),
                    'color' => 'bgWhite',
                    'icon' => "fa-users",
                    "url" => "tipo_activos.index"
                ];
            }
        }


        return $array_infos;
    }
}
