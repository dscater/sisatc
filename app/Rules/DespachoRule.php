<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\PotentiallyTranslatedString;

class DespachoRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('No se encontró ningún producto en el despacho');
            return;
        }

        foreach ($value as $index => $categoria) {
            foreach ($categoria["productos"] as $producto) {
                // stock_previsto
                if ($producto['stock_previsto'] === "" || $producto['stock_previsto'] === null) {
                    $fail("El stock previsto del producto " . ($index + 1) . " es obligatorio.");
                }

                if (!is_numeric($producto['stock_previsto']) || $producto['stock_previsto'] < 0) {
                    $fail("El stock previsto del producto " . ($index + 1) . " debe ser mayor o igual 0");
                }

                foreach ($producto["pedido_detalles"] as $detalle) {
                    // cantidad_total
                    if ($detalle['cantidad_total'] === "" || $detalle['cantidad_total'] === null) {
                        $fail("La cantidad total del producto " . ($producto["nombre"]) . " es obligatorio.");
                    }

                    if (!is_numeric($detalle['cantidad_total']) || $detalle['cantidad_total'] < 0) {
                        $fail("La cantidad total del producto " . ($producto["nombre"]) . " debe ser mayor a 0");
                    }
                }
            }
        }
    }
}
