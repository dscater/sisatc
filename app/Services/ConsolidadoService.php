<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\Consolidado;
use App\Models\ConsolidadoDetalle;
use App\Models\Despacho;
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

class ConsolidadoService
{
    private $modulo = "CONSOLIDADOS";

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
        $consolidados = Consolidado::select("consolidados.*")
            ->where("status", 1)->get();
        return $consolidados;
    }
    /**
     * Lista de consolidados paginado con filtros
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
        $consolidados = Consolidado::select("consolidados.*")
            ->with(["distribuidor:id,nombre", "user:id,nombre"]);
        if (Auth::user()->tipo == 'DISTRIBUIDOR') {
            $consolidados->where("distribuidor_id", Auth::user()->id);
        }

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $consolidados->where("consolidados.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $consolidados->whereBetween("consolidados.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $consolidados->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $consolidados->orderBy($value[0], $value[1]);
            }
        }


        $consolidados = $consolidados->paginate($length, ['*'], 'page', $page);
        return $consolidados;
    }

    /**
     * Crear consolidado
     *
     * @param array $datos
     * @return Consolidado
     */
    public function crear(array $datos): Consolidado
    {

        $fecha_actual = Carbon::now("America/La_Paz")->format("Y-m-d");
        $hora_actual = Carbon::now("America/La_Paz")->format("H:i:s");

        $consolidado = Consolidado::create([
            "distribuidor_id" => $datos["distribuidor_id"],
            "despacho_id" => $datos["despacho_id"],
            "user_id" => Auth::user()->id,
            // "observacion" => $datos["observacion"],
            "fecha" => $fecha_actual,
            "hora" => $hora_actual,
        ]);

        // DETALLES
        foreach ($datos["listCategoriaProductoPedidos"] as $categoria) {

            foreach ($categoria["productos"] as $producto) {
                // stock_previsto
                foreach ($producto["pedido_detalles"] as $detalle) {
                    $pedido = Pedido::findOrFail($detalle["pedido"]["id"]);
                    $pedido->consolidado_id = $consolidado->id;
                    $pedido->save();

                    // registrar ingresos por consolidado
                    $this->pedido_detalle_service->ingresoProductoPorConsolidado($detalle);
                }
            }
        }

        $despacho = Despacho::find($datos["despacho_id"]);
        $despacho->estado = "CONSOLIDADO";
        $despacho->save();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN CONSOLIDADO", $consolidado, null, ["pedidos"]);

        return $consolidado;
    }

    /**
     * Actualizar consolidado
     *
     * @param array $datos
     * @param Consolidado $consolidado
     * @return Consolidado
     */
    public function actualizar(array $datos, Consolidado $consolidado): Consolidado
    {
        $old_consolidado = clone $consolidado;

        $consolidado->update([
            "cliente_id" => $datos["cliente_id"],
            "subtotal" => $datos["subtotal"],
            "descuento" => $datos["descuento"],
            "total" => $datos["total"],
            "observacion" => $datos["observacion"],
        ]);

        // DETALLES
        foreach ($datos["consolidado_detalles"] as $item) {
            $producto = Producto::findOrFail($item["producto_id"]);
            $datos_detalle = [
                "producto_id" => $item["producto_id"],
                "categoria_producto_id" => $producto->categoria_producto_id,
                "presentacion_producto_id" => $item["presentacion_producto_id"],
                "cantidad" => $item["cantidad"],
                "cantidad_total" => $item["cantidad_total"],
                "cantidad_consolidado" => 0,
                "cantidad_entregado" => 0,
                "cantidad_devolucion" => 0,
                "precio" => $item["precio"],
                "subtotal" => $item["subtotal"],
            ];
            if ($item["id"] == 0) {
                $consolidado->consolidado_detalles()->create($datos_detalle);
            } else {
                $consolidado_detalle = ConsolidadoDetalle::find($item["id"]);
                $consolidado_detalle->update($datos_detalle);
            }
        }

        if (isset($datos["eliminados"])) {
            foreach ($datos["eliminados"] as $value) {
                $consolidado_detalle = ConsolidadoDetalle::find($value);
                $consolidado_detalle->delete();
            }
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN CONSOLIDADO", $old_consolidado, $consolidado->withoutRelations());

        return $consolidado;
    }

    /**
     * Eliminar consolidado
     *
     * @param Consolidado $consolidado
     * @return boolean
     */
    public function eliminar(Consolidado $consolidado): bool|Exception
    {
        // TODO: VERIFICAR RELACIONES

        $old_consolidado = clone $consolidado;
        $consolidado->status = 0;
        $consolidado->save();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN CONSOLIDADO", $old_consolidado, $consolidado);

        return true;
    }
}
