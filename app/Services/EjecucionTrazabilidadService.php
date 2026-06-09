<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\EjecucionTrazabilidad;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EjecucionTrazabilidadService
{
    private $modulo = "INCIDENCIAS";

    public function __construct(
        private  CargarArchivoService $cargarArchivoService,
        private HistorialAccionService $historialAccionService,
        private EjecucionArchivoService $ejecucion_archivo_service
    ) {}

    public function listado(): Collection
    {
        $ejecucion_trazabilidads = EjecucionTrazabilidad::select("ejecucion_trazabilidads.*")->get();
        return $ejecucion_trazabilidads;
    }
    /**
     * Lista de ejecucion_trazabilidads paginado con filtros
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
        $ejecucion_trazabilidads = EjecucionTrazabilidad::select("ejecucion_trazabilidads.*")
            ->with(["activo", "ejecucion_archivos"]);

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $ejecucion_trazabilidads->where("ejecucion_trazabilidads.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $ejecucion_trazabilidads->whereBetween("ejecucion_trazabilidads.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $ejecucion_trazabilidads->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $ejecucion_trazabilidads->orderBy($value[0], $value[1]);
            }
        }


        $ejecucion_trazabilidads = $ejecucion_trazabilidads->paginate($length, ['*'], 'page', $page);
        return $ejecucion_trazabilidads;
    }

    /**
     * Crear ejecucion_trazabilidad
     *
     * @param array $datos
     * @return EjecucionTrazabilidad
     */
    public function crear(array $datos): EjecucionTrazabilidad
    {
        $ejecucion_trazabilidad = EjecucionTrazabilidad::create([
            "activo_id" => $datos["activo_id"],
            "estado" => $datos["estado"],
            "trazabilidad" => 0,

            // Campos del sistema
            'user_id'     => auth()->id(),
            'fecha'       => now()->toDateString(),
            'hora'        => now()->format('H:i:s'),
        ]);

        $this->ejecucion_archivo_service->cargarArchivos($ejecucion_trazabilidad, $datos["ejecucion_archivos"], $datos["eliminados_archivos"] ?? []);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA INCIDENCIA", $ejecucion_trazabilidad);

        return $ejecucion_trazabilidad;
    }

    /**
     * Actualizar ejecucion_trazabilidad
     *
     * @param array $datos
     * @param EjecucionTrazabilidad $ejecucion_trazabilidad
     * @return EjecucionTrazabilidad
     */
    public function actualizar(array $datos, EjecucionTrazabilidad $ejecucion_trazabilidad): EjecucionTrazabilidad
    {
        $old_ejecucion_trazabilidad = clone $ejecucion_trazabilidad;

        $ejecucion_trazabilidad->update([
            "activo_id" => $datos["activo_id"],
            "estado" => $datos["estado"],
        ]);

        $this->ejecucion_archivo_service->cargarArchivos($ejecucion_trazabilidad, $datos["ejecucion_archivos"], $datos["eliminados_archivos"] ?? []);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA INCIDENCIA", $old_ejecucion_trazabilidad, $ejecucion_trazabilidad->withoutRelations());

        return $ejecucion_trazabilidad;
    }

    /**
     * Eliminar ejecucion_trazabilidad
     *
     * @param EjecucionTrazabilidad $ejecucion_trazabilidad
     * @return boolean
     */
    public function eliminar(EjecucionTrazabilidad $ejecucion_trazabilidad): bool|Exception
    {
        $old_ejecucion_trazabilidad = clone $ejecucion_trazabilidad;
        $ejecucion_trazabilidad->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA INCIDENCIA", $old_ejecucion_trazabilidad, $ejecucion_trazabilidad);

        return true;
    }
}
