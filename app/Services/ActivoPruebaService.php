<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\ActivoPrueba;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ActivoPruebaService
{
    private $modulo = "GUIONES DE PRUEBAS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

    public function listado($activo_id): Collection
    {
        $activo_pruebas = ActivoPrueba::select("activo_pruebas.*");

        if ($activo_id) {
            $activo_pruebas->where("activo_id", $activo_id);
        }

        $activo_pruebas = $activo_pruebas->get();
        return $activo_pruebas;
    }
    /**
     * Lista de activo_pruebas paginado con filtros
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
        $activo_pruebas = ActivoPrueba::select("activo_pruebas.*")
            ->with(["tipo_activo_prueba"]);

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $activo_pruebas->where("activo_pruebas.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $activo_pruebas->whereBetween("activo_pruebas.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $activo_pruebas->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $activo_pruebas->orderBy($value[0], $value[1]);
            }
        }


        $activo_pruebas = $activo_pruebas->paginate($length, ['*'], 'page', $page);
        return $activo_pruebas;
    }

    /**
     * Crear activo_prueba
     *
     * @param array $datos
     * @return ActivoPrueba
     */
    public function crear(array $datos): ActivoPrueba
    {
        $activo_prueba = ActivoPrueba::create([
            "activo_id" => $datos["activo_id"],
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "modulo" => mb_strtoupper($datos["modulo"]),
            "prueba" => mb_strtoupper($datos["prueba"]),
            "user_id" => Auth::user()->id,
            "fecha" => date("Y-m-d"),
            "hora" => date("H:i:s"),
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN GUIÓN DE PRUEBA", $activo_prueba);

        return $activo_prueba;
    }

    /**
     * Actualizar activo_prueba
     *
     * @param array $datos
     * @param ActivoPrueba $activo_prueba
     * @return ActivoPrueba
     */
    public function actualizar(array $datos, ActivoPrueba $activo_prueba): ActivoPrueba
    {
        $old_activo_prueba = clone $activo_prueba;

        $activo_prueba->update([
            "activo_id" => $datos["activo_id"],
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "modulo" => mb_strtoupper($datos["modulo"]),
            "prueba" => mb_strtoupper($datos["prueba"]),
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN GUIÓN DE PRUEBA", $old_activo_prueba, $activo_prueba->withoutRelations());

        return $activo_prueba;
    }

    /**
     * Eliminar activo_prueba
     *
     * @param ActivoPrueba $activo_prueba
     * @return boolean
     */
    public function eliminar(ActivoPrueba $activo_prueba): bool|Exception
    {
        // TODO: VERIFICAR RELACIONES

        $old_activo_prueba = clone $activo_prueba;
        $activo_prueba->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN GUIÓN DE PRUEBA", $old_activo_prueba, $activo_prueba);

        return true;
    }
}
