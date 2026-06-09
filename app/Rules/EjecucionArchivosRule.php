<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class EjecucionArchivosRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Validar que sea un array
        if (!is_array($value)) {
            $fail('Debes ingresar al menos 1 archivo');
            return;
        }
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'svg'];

        foreach ($value as $index => $item) {
            // Validar estructura básica
            if (!isset($item['file'])) {
                $fail("El campo archivo en la posición " . ($index + 1) . " es requerido.");
                continue;
            }

            $archivo = $item['file'];

            // Si es archivo (nuevo upload)
            if ($archivo instanceof UploadedFile) {
                // Validar tamaño (4MB = 4096 KB)
                if ($archivo->getSize() > 4 * 1024 * 1024) {
                    $fail("El archivo en la posición " . ($index + 1) . " supera los 4MB.");
                }
            }
        }
    }
}
