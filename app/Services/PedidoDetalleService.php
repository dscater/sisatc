<?php

namespace App\Services;

use App\Models\PedidoDetalle;
use App\Models\PresentacionProducto;
use App\Models\Producto;
use App\Services\HistorialAccionService;
use Exception;
use Illuminate\Validation\ValidationException;

class PedidoDetalleService
{

    public function __construct(
        private MovimientoInventarioService $movimiento_inventario_service,
        private ProductoService $producto_service,
    ) {}
    public function recalcularPorDespacho($datos)
    {
        $pedido_detalle = PedidoDetalle::findOrFail($datos["id"]);
        $presentacionProducto = PresentacionProducto::findOrFail($pedido_detalle->presentacion_producto_id);

        $cantidad_total = $datos["cantidad_total"];
        $cantidad_despacho = $datos["cantidad_total"];
        $cantidad = $pedido_detalle->cantidad;
        if ($pedido_detalle->cantidad_total != $datos["cantidad_total"]) {
            $cantidad = ((float)$cantidad_total / (float)$presentacionProducto->equivale);
            $cantidad = round($cantidad, 2);
        }
        $subtotal = (float)$cantidad * (float)$presentacionProducto->precio;

        $datos_detalle = [
            "cantidad" => $cantidad,
            // "cantidad_total" => $cantidad_total,
            "cantidad_despacho" => $cantidad_despacho,
            "cantidad_entregado" => $cantidad_despacho,
            "cantidad_devolucion" => 0,
            "subtotal" => $subtotal,
        ];
        // VERIFICAR STOCKPRODUTO
        $producto = Producto::findOrFail($pedido_detalle->producto_id);
        $verifica_stock = $this->producto_service->verificaStockCantidad($producto->id, $cantidad_total);

        if (!$verifica_stock[0]) {
            throw new Exception("Stock insuficiente del producto $producto->nombre actual $verifica_stock[1] unidades");
        }

        $this->movimiento_inventario_service->registrarMovimiento("Despacho", "EGRESO", $producto, $cantidad_total, $presentacionProducto->precio, "Despacho de producto", "PedidoDetalle", $pedido_detalle->id);

        $pedido_detalle->update($datos_detalle);
    }

    public function ingresoProductoPorConsolidado($datos)
    {
        $pedido_detalle = PedidoDetalle::findOrFail($datos["id"]);
        $producto = Producto::findOrFail($pedido_detalle->producto_id);
        $presentacionProducto = PresentacionProducto::findOrFail($pedido_detalle->presentacion_producto_id);
        if ($pedido_detalle->cantidad_devolucion > 0) {
            $this->movimiento_inventario_service->registrarMovimiento("Consolidado", "INGRESO", $producto, $pedido_detalle->cantidad_devolucion, $presentacionProducto->precio, "Consolidado de Despacho", "PedidoDetalle", $pedido_detalle->id);
        }
    }
}
