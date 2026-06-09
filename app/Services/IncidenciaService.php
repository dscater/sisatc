<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\Incidencia;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class IncidenciaService
{
    private $modulo = "INCIDENCIAS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

    public function listado(): Collection
    {
        $incidencias = Incidencia::select("incidencias.*")->get();
        return $incidencias;
    }
    /**
     * Lista de incidencias paginado con filtros
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
        $incidencias = Incidencia::select("incidencias.*")
            ->with(["tipo_activo"]);

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $incidencias->where("incidencias.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $incidencias->whereBetween("incidencias.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $incidencias->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $incidencias->orderBy($value[0], $value[1]);
            }
        }


        $incidencias = $incidencias->paginate($length, ['*'], 'page', $page);
        return $incidencias;
    }

    /**
     * Crear incidencia
     *
     * @param array $datos
     * @return Incidencia
     */
    public function crear(array $datos): Incidencia
    {
        $incidencia = Incidencia::create([
            "tipo_activo_id" => $datos["tipo_activo_id"],
            "modulo" => $datos["modulo"],
            "tipo_falla" => $datos["tipo_falla"],
            "severidad" => $datos["severidad"],
            "prueba" => $datos["prueba"],
            "resultado" => $datos["resultado"],
            "bug" => $datos["bug"],
            "estado" => $datos["estado"],

            // Campos del sistema
            'user_id'     => auth()->id(),
            'fecha'       => now()->toDateString(),
            'hora'        => now()->format('H:i:s'),
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA INCIDENCIA", $incidencia);

        return $incidencia;
    }

    /**
     * Actualizar incidencia
     *
     * @param array $datos
     * @param Incidencia $incidencia
     * @return Incidencia
     */
    public function actualizar(array $datos, Incidencia $incidencia): Incidencia
    {
        $old_incidencia = clone $incidencia;

        $incidencia->update([
            "tipo_activo_id" => $datos["tipo_activo_id"],
            "modulo" => $datos["modulo"],
            "tipo_falla" => $datos["tipo_falla"],
            "severidad" => $datos["severidad"],
            "prueba" => $datos["prueba"],
            "resultado" => $datos["resultado"],
            "bug" => $datos["bug"],
            "estado" => $datos["estado"],
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA INCIDENCIA", $old_incidencia, $incidencia->withoutRelations());

        return $incidencia;
    }

    /**
     * Eliminar incidencia
     *
     * @param Incidencia $incidencia
     * @return boolean
     */
    public function eliminar(Incidencia $incidencia): bool|Exception
    {
        $old_incidencia = clone $incidencia;
        $incidencia->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA INCIDENCIA", $old_incidencia, $incidencia);

        return true;
    }
}
