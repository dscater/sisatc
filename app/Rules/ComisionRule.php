<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ComisionRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('No se encontró ningún despacho consolidado para generar la comisión.');
            return;
        }
        foreach ($value as $index_despacho => $despacho) {
            foreach ($despacho["categoria_productos"] as $index_categoria => $categoria) {
                foreach ($categoria["productos"] as $index_producto => $producto) {
                    // entrega_distribuidor
                    if ($producto['entrega_distribuidor'] === "" || $producto['entrega_distribuidor'] === null) {
                        $fail("Comisión vacía en despacho " . ($despacho["id"]));
                    }

                    if (!is_numeric($producto['entrega_distribuidor']) || $producto['entrega_distribuidor'] < 0) {
                        $fail("Comisión no valida en despacho " . ($despacho["id"]));
                    }

                    // entrega_vendedor
                    if ($producto['entrega_vendedor'] === "" || $producto['entrega_vendedor'] === null) {
                        $fail("Comisión vacía en despacho " . ($despacho["id"]));
                    }

                    if (!is_numeric($producto['entrega_vendedor']) || $producto['entrega_vendedor'] < 0) {
                        $fail("Comisión no valida en despacho " . ($despacho["id"]));
                    }
                }
            }
        }
    }
}
