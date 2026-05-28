<?php

namespace App\Services;

use App\Models\Permiso;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class PermisoService
{
    protected $arrayPermisos = [
        "ADMINISTRADOR" => [
            "usuarios.paginado",
            "usuarios.index",
            "usuarios.listado",
            "usuarios.listadoAsignacions",
            "usuarios.create",
            "usuarios.store",
            "usuarios.edit",
            "usuarios.show",
            "usuarios.update",
            "usuarios.destroy",
            "usuarios.password",
            "usuarios.byTipo",
            "usuarios.segmentacion_zonas_asignadas",

            "tipo_usuarios.listado",

            "configuracions.index",
            "configuracions.create",
            "configuracions.edit",
            "configuracions.update",
            "configuracions.destroy",

            "tipo_activos.paginado",
            "tipo_activos.index",
            "tipo_activos.listado",
            "tipo_activos.create",
            "tipo_activos.store",
            "tipo_activos.edit",
            "tipo_activos.show",
            "tipo_activos.update",
            "tipo_activos.destroy",

            "activos.paginado",
            "activos.index",
            "activos.listado",
            "activos.create",
            "activos.store",
            "activos.edit",
            "activos.show",
            "activos.update",
            "activos.destroy",

            "entrenamientos.paginado",
            "entrenamientos.index",
            "entrenamientos.listado",
            "entrenamientos.create",
            "entrenamientos.store",
            "entrenamientos.edit",
            "entrenamientos.show",
            "entrenamientos.update",
            "entrenamientos.destroy",

            "incidencias.paginado",
            "incidencias.index",
            "incidencias.listado",
            "incidencias.create",
            "incidencias.store",
            "incidencias.edit",
            "incidencias.show",
            "incidencias.update",
            "incidencias.destroy",

            "ejecucion_trazabilidads.paginado",
            "ejecucion_trazabilidads.index",
            "ejecucion_trazabilidads.listado",
            "ejecucion_trazabilidads.create",
            "ejecucion_trazabilidads.store",
            "ejecucion_trazabilidads.edit",
            "ejecucion_trazabilidads.show",
            "ejecucion_trazabilidads.update",
            "ejecucion_trazabilidads.destroy",

            "reportes.usuarios",
            "reportes.r_usuarios",
        ],
        "JEFE DE AREA" => [],
        "ANALISTA" => [],
    ];

    public function getTiposUsuarios()
    {
        return array_keys($this->arrayPermisos);
    }

    /**
     * Obtener permisos de usuario logeado
     *
     * @return array
     */
    public function getPermisosUser(): array|string
    {
        $user = Auth::user();
        $permisos = [];
        if ($user) {
            return $this->arrayPermisos[$user->tipo];
        }

        return $permisos;
    }
}
