<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class PedidoDetalleRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('Debes ingresar al menos 1 producto');
            return;
        }

        foreach ($value as $index => $detalle) {
            // precio
            if ($detalle['precio'] === "" || $detalle['precio'] === null) {
                $fail("El precio del producto " . ($index + 1) . " es obligatorio.");
            }

            if (!is_numeric($detalle['precio']) || $detalle['precio'] <= 0) {
                $fail("El precio del producto " . ($index + 1) . " debe ser mayor o igual a 0.");
            }

            // cantidad
            if ($detalle['cantidad'] === "" || $detalle['cantidad'] === null) {
                $fail("La cantidad del producto " . ($index + 1) . " es obligatorio.");
            }

            if (!is_numeric($detalle['cantidad']) || $detalle['cantidad'] <= 0) {
                $fail("La cantidad del producto " . ($index + 1) . " debe ser mayor o igual a 0.");
            }
        }
    }
}
