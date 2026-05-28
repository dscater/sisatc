<?php

namespace App\Services;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class MovimientoInventarioService
{

    public function __construct(private ProductoService $productoService) {}

    /**
     * Registrar movimiento de inventario
     *
     * @param string $tipo_registro: Venta,Compra,etc...
     * @param string $ingreso_salida // INGRESO, SALIDA
     * @param Producto $producto
     * @param float $cantidad
     * @param float $precio
     * @param string $detalle
     * @param string $modulo: Venta,Compra,etc (Modelo)
     * @param integer $registro_id  : id del modelo
     * @return void
     */
    public function registrarMovimiento(string $tipo_registro, string $ingreso_salida, Producto $producto, float $cantidad, float $precio, string $detalle = "", string $modulo = "", int $registro_id = 0, $presentacion_producto_id = null): void
    {
        //buscar el ultimo registro y usar sus valores
        $ultimo = MovimientoInventario::where('producto_id', $producto->id)
            ->where("status", 1);
        $ultimo = $ultimo->orderBy('created_at', 'asc')
            ->get()
            ->last();
        $monto = (float)$cantidad * (float)$precio;
        $fecha_actual = Carbon::now("America/La_Paz")->format("Y-m-d");

        // filtrar tipo
        $cantidad_saldo = 0;

        $datos_movimiento = [
            'tipo_registro' => $tipo_registro, //INGRESO, EGRESO, VENTA, COMPRA,etc...
            'registro_id' => $registro_id != 0 ? $registro_id : NULL,
            "modulo" => $modulo,
            'producto_id' => $producto->id,
            'presentacion_producto_id' => $presentacion_producto_id ?? null,
            'detalle' => $detalle,
            'precio' => $precio,
            'tipo_is' => $ingreso_salida,
            'cantidad_saldo' => 0,
            'cu' => $precio,
            'fecha' => $fecha_actual,
        ];

        if ($ingreso_salida == 'INGRESO') {
            if (!$detalle || $detalle == "") {
                $detalle = "INGRESO DE PRODUCTO";
                if ($tipo_registro == "Compra") {
                    $detalle = "COMPRA DE PRODUCTO";
                }
                $datos_movimiento["detalle"] = $detalle;
            }
            if ($ultimo) {
                $cantidad_saldo = (float)$ultimo->cantidad_saldo + (float)$cantidad;
                $monto_saldo = (float)$ultimo->monto_saldo + $monto;
            } else {
                $cantidad_saldo = (float)$cantidad;
                $monto_saldo =  $monto;
            }
            $datos_movimiento["cantidad_ingreso"] = $cantidad;
            $datos_movimiento["monto_ingreso"] = $monto;
        } else {
            // EGRESO
            if (!$detalle || $detalle == "") {
                $detalle = "SALIDA DE PRODUCTO";
                $datos_movimiento["detalle"] = $detalle;
            }
            if ($ultimo) {
                $cantidad_saldo = (float)$ultimo->cantidad_saldo - (float)$cantidad;
                $monto_saldo = (float)$ultimo->monto_saldo - $monto;
            } else {
                throw new Exception("No se encontrarón movimientos para poder registrar un EGRESO");
            }
            $datos_movimiento["cantidad_salida"] = $cantidad;
            $datos_movimiento["monto_salida"] = $monto;
        }

        $datos_movimiento["cantidad_saldo"] = $cantidad_saldo;
        $datos_movimiento["monto_saldo"] = $monto_saldo;

        MovimientoInventario::create($datos_movimiento);

        if ($ingreso_salida == 'INGRESO') {
            // INCREMENTAR STOCK
            // Log::debug("INCREMENTAR STOCK DEL PRODUCTO " . $producto->id . " CANTIDAD: " . $cantidad);
            $this->productoService->incrementarStock($producto->id, $cantidad);
        } else {
            $this->productoService->decrementarStock($producto->id, $cantidad);
        }
    }
}
