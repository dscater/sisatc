<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\Despacho;
use App\Models\DespachoDetalle;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DespachoService
{
    private $modulo = "DESPACHOS";

    public function __construct(
        private CargarArchivoService $cargarArchivoService,
        private HistorialAccionService $historialAccionService,
        private MovimientoInventarioService $movimiento_inventario_service,
        private UserService $user_service,
        private ProductoService $producto_service,
        private PedidoDetalleService $pedido_detalle_service
    ) {}

    public function listado(): Collection
    {
        $despachos = Despacho::select("despachos.*")
            ->where("status", 1)->get();
        return $despachos;
    }
    /**
     * Lista de despachos paginado con filtros
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
        $despachos = Despacho::select("despachos.*")
            ->with(["distribuidor:id,nombre", "user:id,nombre"]);
        if (Auth::user()->tipo == 'DISTRIBUIDOR') {
            $despachos->where("distribuidor_id", Auth::user()->id);
        }

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $despachos->where("despachos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $despachos->whereBetween("despachos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $despachos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $despachos->orderBy($value[0], $value[1]);
            }
        }


        $despachos = $despachos->paginate($length, ['*'], 'page', $page);
        return $despachos;
    }

    /**
     * Crear despacho
     *
     * @param array $datos
     * @return Despacho
     */
    public function crear(array $datos): Despacho
    {

        $fecha_actual = Carbon::now("America/La_Paz")->format("Y-m-d");
        $hora_actual = Carbon::now("America/La_Paz")->format("H:i:s");

        $despacho = Despacho::create([
            "distribuidor_id" => $datos["distribuidor_id"],
            "user_id" => Auth::user()->id,
            "observacion" => $datos["observacion"],
            "fecha" => $fecha_actual,
            "hora" => $hora_actual,
        ]);

        // DETALLES
        foreach ($datos["listCategoriaProductoPedidos"] as $categoria) {

            foreach ($categoria["productos"] as $producto) {
                // stock_previsto
                foreach ($producto["pedido_detalles"] as $detalle) {
                    $pedido = Pedido::findOrFail($detalle["pedido"]["id"]);
                    $pedido->despacho_id = $despacho->id;
                    $pedido->save();

                    // recalcular y registrar despacho
                    $this->pedido_detalle_service->recalcularPorDespacho($detalle);
                }
            }
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN DESPACHO", $despacho, null, ["pedidos"]);

        return $despacho;
    }

    /**
     * Actualizar despacho
     *
     * @param array $datos
     * @param Despacho $despacho
     * @return Despacho
     */
    public function actualizar(array $datos, Despacho $despacho): Despacho
    {
        $old_despacho = clone $despacho;

        $despacho->update([
            "cliente_id" => $datos["cliente_id"],
            "subtotal" => $datos["subtotal"],
            "descuento" => $datos["descuento"],
            "total" => $datos["total"],
            "observacion" => $datos["observacion"],
        ]);

        // DETALLES
        foreach ($datos["despacho_detalles"] as $item) {
            $producto = Producto::findOrFail($item["producto_id"]);
            $datos_detalle = [
                "producto_id" => $item["producto_id"],
                "categoria_producto_id" => $producto->categoria_producto_id,
                "presentacion_producto_id" => $item["presentacion_producto_id"],
                "cantidad" => $item["cantidad"],
                "cantidad_total" => $item["cantidad_total"],
                "cantidad_despacho" => 0,
                "cantidad_entregado" => 0,
                "cantidad_devolucion" => 0,
                "precio" => $item["precio"],
                "subtotal" => $item["subtotal"],
            ];
            if ($item["id"] == 0) {
                $despacho->despacho_detalles()->create($datos_detalle);
            } else {
                $despacho_detalle = DespachoDetalle::find($item["id"]);
                $despacho_detalle->update($datos_detalle);
            }
        }

        if (isset($datos["eliminados"])) {
            foreach ($datos["eliminados"] as $value) {
                $despacho_detalle = DespachoDetalle::find($value);
                $despacho_detalle->delete();
            }
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN DESPACHO", $old_despacho, $despacho->withoutRelations());

        return $despacho;
    }

    /**
     * Eliminar despacho
     *
     * @param Despacho $despacho
     * @return boolean
     */
    public function eliminar(Despacho $despacho): bool|Exception
    {
        // TODO: VERIFICAR RELACIONES

        $old_despacho = clone $despacho;
        $despacho->status = 0;
        $despacho->save();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN DESPACHO", $old_despacho, $despacho);

        return true;
    }
}
