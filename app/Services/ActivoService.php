<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\Activo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ActivoService
{
    private $modulo = "ACTIVOS Y CONFIGURACIÓN";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

    public function listado(): Collection
    {
        $activos = Activo::select("activos.*")->get();
        return $activos;
    }
    /**
     * Lista de activos paginado con filtros
     *
     * @param integer $length
     * @param integer $page
     * @param string $search
     * @param array $columnsSerachLike
     * @param array $columnsFilter
     * @return LengthAwarePaginator
     */
    public function listadoPaginado(int $length, int $page, string $search, array $columnsSerachLike = [], array $columnsFilter = [], array $columnsBetweenFilter = [], array $orderBy = []): LengthAwarePaginator
    {
        $activos = Activo::select("activos.*")
            ->with(["tipo_activo"]);

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $activos->where("activos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $activos->whereBetween("activos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $activos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $activos->orderBy($value[0], $value[1]);
            }
        }


        $activos = $activos->paginate($length, ['*'], 'page', $page);
        return $activos;
    }

    /**
     * Crear activo
     *
     * @param array $datos
     * @return Activo
     */
    public function crear(array $datos): Activo
    {
        $activo = Activo::create([
            "codigo" => mb_strtoupper($datos["codigo"]),
            "nombre" => mb_strtoupper($datos["nombre"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "tipo_activo_id" => $datos["tipo_activo_id"],
            "version" => $datos["version"],
            "user_id" => Auth::user()->id,
            "fecha_registro" => date("Y-m-d"),
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN ACTIVO Y CONFIGURACIÓN", $activo);

        return $activo;
    }

    /**
     * Actualizar activo
     *
     * @param array $datos
     * @param Activo $activo
     * @return Activo
     */
    public function actualizar(array $datos, Activo $activo): Activo
    {
        $old_activo = clone $activo;

        $activo->update([
            "codigo" => mb_strtoupper($datos["codigo"]),
            "nombre" => mb_strtoupper($datos["nombre"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "tipo_activo_id" => $datos["tipo_activo_id"],
            "version" => $datos["version"],
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN ACTIVO Y CONFIGURACIÓN", $old_activo, $activo->withoutRelations());

        return $activo;
    }

    /**
     * Eliminar activo
     *
     * @param Activo $activo
     * @return boolean
     */
    public function eliminar(Activo $activo): bool|Exception
    {
        // TODO: VERIFICAR RELACIONES

        $old_activo = clone $activo;
        $activo->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN ACTIVO Y CONFIGURACIÓN", $old_activo, $activo);

        return true;
    }
}
