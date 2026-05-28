<?php

namespace App\Rules;

use App\Models\CertificadoDetalle;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\PotentiallyTranslatedString;

class CertificadoDetalleRule implements ValidationRule
{

    protected $conCategoria;
    protected $conArchivo;

    public function __construct($conCategoria = true, $conArchivo = true)
    {
        $this->conCategoria = $conCategoria;
        $this->conArchivo = $conArchivo;
    }


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
            // precio
            if ($detalle['precio'] === "" || $detalle['precio'] === null) {
                $fail("El precio del certificado " . ($index + 1) . " es obligatorio.");
            }

            if (!is_numeric($detalle['precio']) || $detalle['precio'] < 0) {
                $fail("El precio del certificado " . ($index + 1) . " debe ser mayor o igual a 0.");
            }

            // tipo_certificado_id
            if (empty($detalle['tipo_certificado_id'])) {
                $fail("El tipo de certificado del certificado " . ($index + 1) . " es obligatorio.");
            }

            $id = $detalle["id"] ?? 0;
            if ($this->conArchivo) {
                // archivo
                if (!isset($detalle['archivo']) || $detalle['archivo'] === null) {
                    if ($detalle['tipo_certificado_id'] == 1) {
                        $fail("El archivo del certificado " . ($index + 1) . " es obligatorio.");
                    }
                } else if (!is_string($detalle["archivo"])) {
                    // validar tamaño (2MB = 2048 KB)
                    if ($detalle['archivo']->getSize() > 2048 * 1024) {
                        $fail("El archivo del certificado " . ($index + 1) . " no debe superar los 2MB.");
                    }
                }
            }
            if ($this->conCategoria) {
                // categoria
                if ($detalle['tipo_certificado_id'] == 1) {
                    if ($detalle['categoria'] === "" || $detalle['categoria'] === null) {
                        $fail("La categoría del certificado " . ($index + 1) . " es obligatorio." . $detalle['categoria']);
                    }
                }
            }
        }
    }
}
