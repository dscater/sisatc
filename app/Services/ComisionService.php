<?php

namespace App\Services;

use App\Models\CategoriaProducto;
use App\Services\HistorialAccionService;
use App\Models\Comision;
use App\Models\ComisionDetalle;
use App\Models\Despacho;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\PresentacionProducto;
use App\Models\Producto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ComisionService
{
    private $modulo = "COMISIONES";

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
        $comisions = Comision::select("comisions.*")
            ->where("status", 1)->get();
        return $comisions;
    }
    /**
     * Lista de comisions paginado con filtros
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
        $comisions = Comision::select("comisions.*")
            ->with(["distribuidor:id,nombre", "user:id,nombre"]);
        if (Auth::user()->tipo == 'DISTRIBUIDOR') {
            $comisions->where("distribuidor_id", Auth::user()->id);
        }

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $comisions->where("comisions.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $comisions->whereBetween("comisions.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $comisions->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $comisions->orderBy($value[0], $value[1]);
            }
        }


        $comisions = $comisions->paginate($length, ['*'], 'page', $page);
        return $comisions;
    }

    /**
     * Crear comision
     *
     * @param array $datos
     * @return Comision
     */
    public function crear(array $datos): Comision
    {
        $fecha_actual = Carbon::now("America/La_Paz")->format("Y-m-d");
        $hora_actual = Carbon::now("America/La_Paz")->format("H:i:s");

        $comision = Comision::create([
            "distribuidor_id" => $datos["distribuidor_id"],
            // "despacho_id" => $datos["despacho_id"],
            "user_id" => Auth::user()->id,
            // "observacion" => $datos["observacion"],
            "fecha" => $fecha_actual,
            "hora" => $hora_actual,
        ]);

        // DETALLES
        foreach ($datos["listDespachosConsolidadosData"] as $despacho) {
            foreach ($despacho["categoria_productos"] as $categoria) {
                foreach ($categoria["productos"] as $producto) {
                    $comision->comision_detalles()->create([
                        "despacho_id" => $despacho["id"],
                        "categoria_producto_id" => $producto["categoria_producto_id"],
                        "producto_id" => $producto["id"],
                        "cantidad" => $producto["cantidad_entregado"],
                        "total" => $producto["monto_vendido"],
                        "comision_distribuidor" => $producto["comision_distribuidor"],
                        "comision_vendedor" => $producto["comision_vendedor"],
                        "entrega_distribuidor" => $producto["entrega_distribuidor"],
                        "entrega_vendedor" => $producto["entrega_vendedor"],
                        "detalle_presentacion" => $producto["presentacions"]
                    ]);
                }
            }

            $despacho = Despacho::find($despacho["id"]);
            $despacho->comision = 1;
            $despacho->save();
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA COMISIÓN", $comision, null, ["comision_detalles"]);

        return $comision;
    }

    public function consultaComision($comision_id, $distribuidor_id)
    {
        return ComisionDetalle::select("despacho_id")
            ->where("comision_id", $comision_id)
            ->groupBy("despacho_id")
            ->get()
            ->map(function ($comision_detalle) use ($comision_id) {
                $despacho_id = $comision_detalle->despacho_id;
                $comision_detalle->categoria_productos = CategoriaProducto::whereHas("pedido_detalles", function ($q) use ($despacho_id,) {
                    $q->whereHas("pedido", function ($sub) use ($despacho_id) {
                        $sub->where("despacho_id", $despacho_id);
                    });
                })->distinct()
                    ->orderBy("nombre", "asc")->get()
                    ->map(function ($categoria) use ($despacho_id, $comision_id, $comision_detalle) {
                        $categoria->productos = Producto::whereHas("pedido_detalles", function ($q) use ($categoria,  $despacho_id,) {
                            $q->where("categoria_producto_id", $categoria->id);
                            $q->whereHas("pedido", function ($sub) use ($despacho_id) {
                                $sub->where("despacho_id", $despacho_id);
                            });
                        })->orderBy("nombre", "asc")
                            ->get()
                            ->map(function ($producto) use ($despacho_id, $comision_id, $comision_detalle) {
                                $producto->cantidad_entregado = PedidoDetalle::where("producto_id", $producto->id)
                                    ->whereHas("pedido", function ($sub) use ($despacho_id) {
                                        $sub->where("despacho_id", $despacho_id);
                                    })->sum("cantidad_entregado");

                                $producto->monto_vendido = PedidoDetalle::where("producto_id", $producto->id)
                                    ->whereHas("pedido", function ($sub) use ($despacho_id) {
                                        $sub->where("despacho_id", $despacho_id);
                                    })->sum("subtotal");

                                // $producto->presentacions = PresentacionProducto::whereHas("pedido_detalles", function ($q) use ($producto,  $despacho_id) {
                                //     $q->where("producto_id", $producto->id);
                                //     $q->whereHas("pedido", function ($sub) use ($despacho_id) {
                                //         $sub->where("despacho_id", $despacho_id);
                                //     });
                                $producto->presentacions = PresentacionProducto::whereHas("pedido_detalles", function ($q) use ($producto,  $despacho_id) {
                                    $q->where("producto_id", $producto->id);
                                    $q->whereHas("pedido", function ($sub) use ($despacho_id) {
                                        $sub->where("despacho_id", $despacho_id);
                                    });
                                })->orderBy("nombre", "asc")
                                    ->get()
                                    ->map(function ($presentacion) use (
                                        $producto,
                                        $despacho_id,
                                    ) {
                                        $presentacion->ver = false;
                                        $presentacion->total_cantidad = PedidoDetalle::where("producto_id", $producto->id)
                                            ->where("presentacion_producto_id", $presentacion->id)
                                            ->whereHas("pedido", function ($sub) use ($despacho_id) {
                                                $sub->where("despacho_id", $despacho_id);
                                            })->sum("cantidad");
                                        $presentacion->cantidad_presentacion = PedidoDetalle::where("producto_id", $producto->id)
                                            ->where("presentacion_producto_id", $presentacion->id)
                                            ->whereHas("pedido", function ($sub) use ($despacho_id,) {
                                                $sub->where("despacho_id", $despacho_id);
                                            })->sum("cantidad");

                                        return $presentacion;
                                    });

                                $producto->presentacions = ComisionDetalle::where("comision_id", $comision_id)
                                    ->where("despacho_id", $despacho_id)
                                    ->where("producto_id", $producto->id)
                                    ->get()->first()->detalle_presentacion;
                                // Asignar totales al producto
                                $producto->comision_distribuidor = ComisionDetalle::where("comision_id", $comision_id)
                                    ->where("despacho_id", $despacho_id)
                                    ->where("producto_id", $producto->id)
                                    ->sum("comision_distribuidor");
                                $producto->comision_vendedor = ComisionDetalle::where("comision_id", $comision_id)
                                    ->where("despacho_id", $despacho_id)
                                    ->where("producto_id", $producto->id)
                                    ->sum("comision_vendedor");

                                $producto->entrega_distribuidor = ComisionDetalle::where("comision_id", $comision_id)
                                    ->where("despacho_id", $despacho_id)
                                    ->where("producto_id", $producto->id)
                                    ->sum("entrega_distribuidor");
                                $producto->entrega_vendedor = ComisionDetalle::where("comision_id", $comision_id)
                                    ->where("despacho_id", $despacho_id)
                                    ->where("producto_id", $producto->id)
                                    ->sum("entrega_vendedor");

                                return $producto;
                            });
                        return $categoria;
                    });
                return $comision_detalle;
            });
    }


    /**
     * Eliminar comision
     *
     * @param Comision $comision
     * @return boolean
     */
    public function eliminar(Comision $comision): bool|Exception
    {
        // TODO: VERIFICAR RELACIONES

        $old_comision = clone $comision;
        $comision->status = 0;
        $comision->save();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA COMISIÓN", $old_comision, $comision);

        return true;
    }
}
