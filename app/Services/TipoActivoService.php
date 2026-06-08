<?php

namespace App\Services;

use App\Models\Activo;
use App\Services\HistorialAccionService;
use App\Models\TipoActivo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class TipoActivoService
{
    private $modulo = "TIPO DE ACTIVOS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

    public function listado(): Collection
    {
        $tipo_activos = TipoActivo::select("tipo_activos.*")->get();
        return $tipo_activos;
    }
    /**
     * Lista de tipo_activos paginado con filtros
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
        $tipo_activos = TipoActivo::select("tipo_activos.*");

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $tipo_activos->where("tipo_activos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $tipo_activos->whereBetween("tipo_activos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $tipo_activos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $tipo_activos->orderBy($value[0], $value[1]);
            }
        }


        $tipo_activos = $tipo_activos->paginate($length, ['*'], 'page', $page);
        return $tipo_activos;
    }

    /**
     * Crear tipo_activo
     *
     * @param array $datos
     * @return TipoActivo
     */
    public function crear(array $datos): TipoActivo
    {
        $tipo_activo = TipoActivo::create([
            "nombre" => mb_strtoupper($datos["nombre"]),
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN TIPO DE ACTIVO", $tipo_activo);

        return $tipo_activo;
    }

    /**
     * Actualizar tipo_activo
     *
     * @param array $datos
     * @param TipoActivo $tipo_activo
     * @return TipoActivo
     */
    public function actualizar(array $datos, TipoActivo $tipo_activo): TipoActivo
    {
        $old_tipo_activo = clone $tipo_activo;

        $tipo_activo->update([
            "nombre" => mb_strtoupper($datos["nombre"]),
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN TIPO DE ACTIVO", $old_tipo_activo, $tipo_activo->withoutRelations());

        return $tipo_activo;
    }

    /**
     * Eliminar tipo_activo
     *
     * @param TipoActivo $tipo_activo
     * @return boolean
     */
    public function eliminar(TipoActivo $tipo_activo): bool|Exception
    {
        $usos = Activo::where("tipo_activo_id", $tipo_activo->id)->count();
        if ($usos > 0) {
            throw ValidationException::withMessages(["tipo_activo_id" => "No se puede eliminar el tipo de activo porque está asociado a uno o más activos."]);
        }

        $old_tipo_activo = clone $tipo_activo;
        $tipo_activo->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN TIPO DE ACTIVO", $old_tipo_activo, $tipo_activo);

        return true;
    }
}
