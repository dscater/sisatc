<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\AsignacionZona;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class AsignacionZonaService
{
    private $modulo = "ASIGNACIÓN DE ZONAS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

    public function listado(): Collection
    {
        $asignacion_zonas = AsignacionZona::select("asignacion_zonas.*")->get();
        return $asignacion_zonas;
    }
    /**
     * Lista de asignacion_zonas paginado con filtros
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
        $asignacion_zonas = AsignacionZona::select("asignacion_zonas.*")
            ->with(["departamento:id,nombre", "provincia:id,nombre", "ciudad:id,nombre"]);

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $asignacion_zonas->where("asignacion_zonas.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $asignacion_zonas->whereBetween("asignacion_zonas.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $asignacion_zonas->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $asignacion_zonas->orderBy($value[0], $value[1]);
            }
        }


        $asignacion_zonas = $asignacion_zonas->paginate($length, ['*'], 'page', $page);
        return $asignacion_zonas;
    }

    /**
     * Crear asignacion_zona
     *
     * @param array $datos
     * @return AsignacionZona
     */
    public function crear(array $datos)
    {
        $asignacion_zona = AsignacionZona::where("user_id", $datos["user_id"])
            ->where("segmentacion_zona_id", $datos["segmentacion_zona_id"])
            ->get()->first();

        if ($asignacion_zona) {
            if (!$datos["checked"]) {
                // ELIMINACION
                $old_asignacion_zona = clone $asignacion_zona;
                $asignacion_zona->delete();

                $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA ASIGNACIÓN DE ZONA", $old_asignacion_zona);
                return true;
            }
            return false;
        }

        $asignacion_zona = AsignacionZona::create([
            "segmentacion_zona_id" => $datos["segmentacion_zona_id"],
            "user_id" => $datos["user_id"],
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA ASIGNACIÓN DE ZONA", $asignacion_zona);

        return $asignacion_zona;
    }

    /**
     * Actualizar asignacion_zona
     *
     * @param array $datos
     * @param AsignacionZona $asignacion_zona
     * @return AsignacionZona
     */
    public function actualizar(array $datos, AsignacionZona $asignacion_zona): AsignacionZona
    {
        $old_asignacion_zona = clone $asignacion_zona;

        $asignacion_zona->update([
            "segmentacion_zona_id" => $datos["segmentacion_zona_id"],
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA ASIGNACIÓN DE ZONA", $old_asignacion_zona, $asignacion_zona->withoutRelations());

        return $asignacion_zona;
    }

    /**
     * Eliminar asignacion_zona
     *
     * @param AsignacionZona $asignacion_zona
     * @return boolean
     */
    public function eliminar(AsignacionZona $asignacion_zona): bool|Exception
    {
        $old_asignacion_zona = clone $asignacion_zona;
        $asignacion_zona->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA ASIGNACIÓN DE ZONA", $old_asignacion_zona, $asignacion_zona);

        return true;
    }
}
