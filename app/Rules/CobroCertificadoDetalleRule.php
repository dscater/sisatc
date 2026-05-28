<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\PotentiallyTranslatedString;

class CobroCertificadoDetalleRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('Debes ingresar al menos 1 certificado');
            return;
        }

        foreach ($value as $index => $detalle) {
            if (!$detalle["con_saldo"]) {
                $fail('Debes marcar todos los pagos pendientes');
            }
        }
    }
}
