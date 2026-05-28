<?php

namespace App\Services;

use App\Models\PresentacionProducto;
use App\Models\User;
use App\Services\HistorialAccionService;
use Exception;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class PresentacionProductoService
{
    private $modulo = "PRESENTACIÓN DE PRODUCTOS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

    public function listado($producto_id = 0): Collection
    {
        $presentacion_productos = PresentacionProducto::select("presentacion_productos.*");

        if ($producto_id && $producto_id != 0) {
            $presentacion_productos->where("producto_id", $producto_id);
        }

        $presentacion_productos = $presentacion_productos->orderBy("id", "desc")->get();
        return $presentacion_productos;
    }
    /**
     * Lista de presentacion_productos paginado con filtros
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
        $presentacion_productos = PresentacionProducto::select("presentacion_productos.*")
            ->with(["categoria_presentacion_producto:id,nombre"])
            ->withCount("presentacion_presentacion_productos");;

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $presentacion_productos->where("presentacion_productos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $presentacion_productos->whereBetween("presentacion_productos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $presentacion_productos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $presentacion_productos->orderBy($value[0], $value[1]);
            }
        }


        $presentacion_productos = $presentacion_productos->paginate($length, ['*'], 'page', $page);
        return $presentacion_productos;
    }

    /**
     * Crear presentacion_producto
     *
     * @param array $datos
     * @return PresentacionProducto
     */
    public function crear(array $datos): PresentacionProducto
    {
        $presentacion_producto = PresentacionProducto::create([
            "producto_id" => $datos["producto_id"],
            "nombre" => $datos["nombre"],
            "equivale" => $datos["equivale"],
            "precio" => $datos["precio"],
            "comi_distribuidor" => $datos["comi_distribuidor"],
            "comi_vendedor" => $datos["comi_vendedor"],
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA PRESENTACIÓN DE PRODUCTO", $presentacion_producto);

        return $presentacion_producto;
    }

    /**
     * Actualizar presentacion_producto
     *
     * @param array $datos
     * @param PresentacionProducto $presentacion_producto
     * @return PresentacionProducto
     */
    public function actualizar(array $datos, PresentacionProducto $presentacion_producto): PresentacionProducto
    {
        $old_presentacion_producto = clone $presentacion_producto;

        $presentacion_producto->update([
            "producto_id" => $datos["producto_id"],
            "nombre" => $datos["nombre"],
            "equivale" => $datos["equivale"],
            "precio" => $datos["precio"],
            "comi_distribuidor" => $datos["comi_distribuidor"],
            "comi_vendedor" => $datos["comi_vendedor"],
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA PRESENTACIÓN DE PRODUCTO", $old_presentacion_producto, $presentacion_producto->withoutRelations());

        return $presentacion_producto;
    }

    /**
     * Eliminar presentacion_producto
     *
     * @param PresentacionProducto $presentacion_producto
     * @return boolean
     */
    public function eliminar(PresentacionProducto $presentacion_producto): bool|Exception
    {
        // TODO: VERIFICAR RELACIONES

        $old_presentacion_producto = clone $presentacion_producto;
        $presentacion_producto->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA PRESENTACIÓN DE PRODUCTO", $old_presentacion_producto, $presentacion_producto);

        return true;
    }
}
