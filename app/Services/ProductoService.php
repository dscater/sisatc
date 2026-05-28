<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\User;
use App\Services\HistorialAccionService;
use Exception;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProductoService
{
    private $modulo = "PRODUCTOS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

    public function listado(): Collection
    {
        $productos = Producto::select("productos.*")
            ->with(["presentacion_productos"])
            ->where("estado", 1)->get();
        return $productos;
    }
    /**
     * Lista de productos paginado con filtros
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
        $productos = Producto::select("productos.*")
            ->with(["categoria_producto:id,nombre"])
            ->withCount("presentacion_productos");;

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $productos->where("productos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $productos->whereBetween("productos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $productos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $productos->orderBy($value[0], $value[1]);
            }
        }


        $productos = $productos->paginate($length, ['*'], 'page', $page);
        return $productos;
    }

    /**
     * Crear producto
     *
     * @param array $datos
     * @return Producto
     */
    public function crear(array $datos): Producto
    {
        $producto = Producto::create([
            "nombre" => $datos["nombre"],
            "descripcion" => $datos["descripcion"],
            "categoria_producto_id" => $datos["categoria_producto_id"],
            "stock_min" => $datos["stock_min"],
            "estado" => $datos["estado"],
        ]);

        // cargar imagen
        if (isset($datos["imagen"]) && !is_string($datos["imagen"])) {
            $this->cargarFoto($producto, $datos["imagen"]);
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN PRODUCTO", $producto);

        return $producto;
    }

    /**
     * Actualizar producto
     *
     * @param array $datos
     * @param Producto $producto
     * @return Producto
     */
    public function actualizar(array $datos, Producto $producto): Producto
    {
        $old_producto = clone $producto;

        $producto->update([
            "nombre" => $datos["nombre"],
            "descripcion" => $datos["descripcion"],
            "categoria_producto_id" => $datos["categoria_producto_id"],
            "stock_min" => $datos["stock_min"],
            "estado" => $datos["estado"],
        ]);

        // cargar imagen
        if (isset($datos["imagen"]) && !is_string($datos["imagen"])) {
            $this->cargarFoto($producto, $datos["imagen"]);
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN PRODUCTO", $old_producto, $producto->withoutRelations());

        return $producto;
    }

    /**
     * Eliminar producto
     *
     * @param Producto $producto
     * @return boolean
     */
    public function eliminar(Producto $producto): bool|Exception
    {
        // TODO: VERIFICAR RELACIONES

        $old_producto = clone $producto;
        $producto->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN PRODUCTO", $old_producto, $producto);

        return true;
    }

    /**
     * Cargar imagen
     *
     * @param Producto $producto
     * @param UploadedFile $imagen
     * @return void
     */
    public function cargarFoto(Producto $producto, UploadedFile $imagen): void
    {
        if ($producto->imagen) {
            \File::delete(public_path("imgs/productos/" . $producto->imagen));
        }

        $nombre = $producto->id . time();
        $producto->imagen = $this->cargarArchivoService->cargarArchivo($imagen, public_path("imgs/productos"), $nombre);
        $producto->save();
    }

    public function incrementarStock(int $producto_id, int $cantidad = 1)
    {
        $producto = Producto::findOrFail($producto_id);
        $producto->stock_actual = (float)$producto->stock_actual + $cantidad;
        $producto->save();
        return $producto;
    }
    public function decrementarStock(int $producto_id, int $cantidad = 1)
    {
        $producto = Producto::findOrFail($producto_id);
        // validar stock
        if (!$this->verificaStock($producto_id, $cantidad)) {
            throw new Exception("Stock insuficiente del producto " . $producto->nombre . ", disponible " . $producto->stock_actual);
        }

        $producto->stock_actual = (float)$producto->stock_actual - $cantidad;
        $producto->save();

        return $producto;
    }

    public function verificaStock($producto_id, $cantidad): bool
    {
        $producto = Producto::findOrFail($producto_id);
        $disponible = false;
        if ($producto->stock_actual >= $cantidad) {
            $disponible = true;
        }

        return $disponible;
    }

    public function verificaStockCantidad($producto_id, $cantidad): array
    {
        $producto = Producto::findOrFail($producto_id);
        $disponible = false;
        if ($producto->stock_actual >= $cantidad) {
            $disponible = true;
        }

        return [$disponible, $producto->stock_actual];
    }

    public function validaStockCantidad($producto_id, $cantidad)
    {
        $producto = Producto::findOrFail($producto_id);
        $disponible = false;
        if ($producto->stock_actual >= $cantidad) {
            $disponible = true;
        }
        if (!$disponible) {
            throw new Exception("Stock insuficiente del producto $producto->nombre, stock actual " . $producto->stock_actual, 422);
            return false;
        }
        return true;
    }
}
