<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\CategoriaProducto;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class CategoriaProductoService
{
    private $modulo = "CATEGORÍA DE PRODUCTOS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

    public function listado(): Collection
    {
        $categoria_productos = CategoriaProducto::select("categoria_productos.*")->get();
        return $categoria_productos;
    }
    /**
     * Lista de categoria_productos paginado con filtros
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
        $categoria_productos = CategoriaProducto::select("categoria_productos.*");

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $categoria_productos->where("categoria_productos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $categoria_productos->whereBetween("categoria_productos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $categoria_productos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $categoria_productos->orderBy($value[0], $value[1]);
            }
        }


        $categoria_productos = $categoria_productos->paginate($length, ['*'], 'page', $page);
        return $categoria_productos;
    }

    /**
     * Crear categoria_producto
     *
     * @param array $datos
     * @return CategoriaProducto
     */
    public function crear(array $datos): CategoriaProducto
    {
        $categoria_producto = CategoriaProducto::create([
            "nombre" => $datos["nombre"],
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA CATEGORÍA DE PRODUCTO", $categoria_producto);

        return $categoria_producto;
    }

    /**
     * Actualizar categoria_producto
     *
     * @param array $datos
     * @param CategoriaProducto $categoria_producto
     * @return CategoriaProducto
     */
    public function actualizar(array $datos, CategoriaProducto $categoria_producto): CategoriaProducto
    {
        $old_categoria_producto = clone $categoria_producto;

        $categoria_producto->update([
            "nombre" => $datos["nombre"],
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA CATEGORÍA DE PRODUCTO", $old_categoria_producto, $categoria_producto->withoutRelations());

        return $categoria_producto;
    }

    /**
     * Eliminar categoria_producto
     *
     * @param CategoriaProducto $categoria_producto
     * @return boolean
     */
    public function eliminar(CategoriaProducto $categoria_producto): bool|Exception
    {
        // TODO: VERIFICAR RELACIONES

        $old_categoria_producto = clone $categoria_producto;
        $categoria_producto->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA CATEGORÍA DE PRODUCTO", $old_categoria_producto, $categoria_producto);

        return true;
    }
}
