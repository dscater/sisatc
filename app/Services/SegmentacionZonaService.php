<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\SegmentacionZona;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SegmentacionZonaService
{
    private $modulo = "SEGMENTACIÓN DE ZONAS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService, private UserService $user_service) {}

    public function listado(): Collection
    {
        $segmentacion_zonas = SegmentacionZona::select("segmentacion_zonas.*");

        if (Auth::user()->tipo != 'ADMINISTRADOR') {
            $segmentacion_zona = $this->user_service->getSegmentacionZona(Auth::user()->id);
            $segmentacion_zonas->where("id", $segmentacion_zona?->id);
        }

        $segmentacion_zonas = $segmentacion_zonas->get();
        return $segmentacion_zonas;
    }
    /**
     * Lista de segmentacion_zonas paginado con filtros
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
        $segmentacion_zonas = SegmentacionZona::select("segmentacion_zonas.*")
            ->with(["departamento:id,nombre", "provincia:id,nombre", "ciudad:id,nombre"]);

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $segmentacion_zonas->where("segmentacion_zonas.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $segmentacion_zonas->whereBetween("segmentacion_zonas.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $segmentacion_zonas->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $segmentacion_zonas->orderBy($value[0], $value[1]);
            }
        }


        $segmentacion_zonas = $segmentacion_zonas->paginate($length, ['*'], 'page', $page);
        return $segmentacion_zonas;
    }

    /**
     * Crear segmentacion_zona
     *
     * @param array $datos
     * @return SegmentacionZona
     */
    public function crear(array $datos): SegmentacionZona
    {
        $segmentacion_zona = SegmentacionZona::create([
            "zona" => $datos["zona"],
            "departamento_id" => $datos["departamento_id"],
            "provincia_id" => $datos["provincia_id"],
            "ciudad_id" => $datos["ciudad_id"],
            "color" => $datos["color"],
            "segmentacion" => $datos["segmentacion"],
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA SEGMENTACIÓN DE ZONA", $segmentacion_zona);

        return $segmentacion_zona;
    }

    /**
     * Actualizar segmentacion_zona
     *
     * @param array $datos
     * @param SegmentacionZona $segmentacion_zona
     * @return SegmentacionZona
     */
    public function actualizar(array $datos, SegmentacionZona $segmentacion_zona): SegmentacionZona
    {
        $old_segmentacion_zona = clone $segmentacion_zona;

        $segmentacion_zona->update([
            "zona" => $datos["zona"],
            "departamento_id" => $datos["departamento_id"],
            "provincia_id" => $datos["provincia_id"],
            "ciudad_id" => $datos["ciudad_id"],
            "color" => $datos["color"],
            "segmentacion" => $datos["segmentacion"],
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA SEGMENTACIÓN DE ZONA", $old_segmentacion_zona, $segmentacion_zona->withoutRelations());

        return $segmentacion_zona;
    }

    /**
     * Eliminar segmentacion_zona
     *
     * @param SegmentacionZona $segmentacion_zona
     * @return boolean
     */
    public function eliminar(SegmentacionZona $segmentacion_zona): bool|Exception
    {
        // TODO: VERIFICAR RELACIONES

        $old_segmentacion_zona = clone $segmentacion_zona;
        $segmentacion_zona->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA SEGMENTACIÓN DE ZONA", $old_segmentacion_zona, $segmentacion_zona);

        return true;
    }
}
