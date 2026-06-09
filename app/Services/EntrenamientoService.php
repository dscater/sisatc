<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\Entrenamiento;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EntrenamientoService
{
    private $modulo = "ENTRENAMIENTOS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

    public function listado($activo_id): Collection
    {
        $entrenamientos = Entrenamiento::select("entrenamientos.*");

        if ($activo_id) {
            $entrenamientos->where("activo_id", $activo_id);
        }

        $entrenamientos = $entrenamientos->get();
        return $entrenamientos;
    }
    /**
     * Lista de entrenamientos paginado con filtros
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
        $entrenamientos = Entrenamiento::select("entrenamientos.*")
            ->with(["tipo_entrenamiento"]);

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $entrenamientos->where("entrenamientos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $entrenamientos->whereBetween("entrenamientos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $entrenamientos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $entrenamientos->orderBy($value[0], $value[1]);
            }
        }


        $entrenamientos = $entrenamientos->paginate($length, ['*'], 'page', $page);
        return $entrenamientos;
    }

    /**
     * Crear entrenamiento
     *
     * @param array $datos
     * @return Entrenamiento
     */
    public function crear(array $datos): Entrenamiento
    {
        $entrenamiento = Entrenamiento::create([
            "activo_id" => $datos["activo_id"],
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "modulo" => mb_strtoupper($datos["modulo"]),
            "prueba" => mb_strtoupper($datos["prueba"]),
            "user_id" => Auth::user()->id,
            "fecha" => date("Y-m-d"),
            "hora" => date("H:i:s"),
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN GUIÓN DE PRUEBA", $entrenamiento);

        return $entrenamiento;
    }

    /**
     * Actualizar entrenamiento
     *
     * @param array $datos
     * @param Entrenamiento $entrenamiento
     * @return Entrenamiento
     */
    public function actualizar(array $datos, Entrenamiento $entrenamiento): Entrenamiento
    {
        $old_entrenamiento = clone $entrenamiento;

        $entrenamiento->update([
            "activo_id" => $datos["activo_id"],
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "modulo" => mb_strtoupper($datos["modulo"]),
            "prueba" => mb_strtoupper($datos["prueba"]),
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN GUIÓN DE PRUEBA", $old_entrenamiento, $entrenamiento->withoutRelations());

        return $entrenamiento;
    }

    /**
     * Eliminar entrenamiento
     *
     * @param Entrenamiento $entrenamiento
     * @return boolean
     */
    public function eliminar(Entrenamiento $entrenamiento): bool|Exception
    {
        // TODO: VERIFICAR RELACIONES

        $old_entrenamiento = clone $entrenamiento;
        $entrenamiento->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN GUIÓN DE PRUEBA", $old_entrenamiento, $entrenamiento);

        return true;
    }
}
