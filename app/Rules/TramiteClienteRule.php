<?php

namespace App\Rules;

use App\Models\Cliente;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class TramiteClienteRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('Debes agregar al menos 1 paciente');
            return;
        }

        foreach ($value as $index => $detalle) {
            $cliente = $detalle["cliente"];
            $existente = $detalle["existente"];
            if (!$existente && !$detalle["cliente_id"]) {
                // ci
                if ($cliente['ci'] === "" || $cliente['ci'] === null) {
                    $fail("El Nro. de C.I. en la fila " . ($index + 1) . " es obligatorio.");
                    continue;
                }

                // nombre
                if ($cliente['nombre'] === "" || $cliente['nombre'] === null) {
                    $fail("El nombre en la fila " . ($index + 1) . " es obligatorio.");
                    continue;
                }
                // paterno
                if ($cliente['paterno'] === "" || $cliente['paterno'] === null) {
                    $fail("El Ap. Paterno en la fila " . ($index + 1) . " es obligatorio.");
                    continue;
                }

                // fecha_nac
                if ($cliente['fecha_nac'] === "" || $cliente['fecha_nac'] === null) {
                    $fail("La Fecha de Nacimiento en la fila " . ($index + 1) . " es obligatorio.");
                    continue;
                }

                // VALIDAR DUPLICADOS EN BD
                $duplicado = false;
                if ($cliente['ci'] != "") {
                    if ($detalle["id"] == 0) {
                        // NUEVO TRAMITE
                        if ($cliente['complemento'] != '') {
                            $cliente = Cliente::where("ci", $cliente['ci'])
                                ->where("complemento", $cliente['complemento'])
                                ->get()->first();
                            if ($cliente) {
                                $duplicado = true;
                            }
                        } else {
                            $cliente = Cliente::where("ci", $cliente['ci'])
                                ->get()->first();
                            if ($cliente) {
                                $duplicado = true;
                            }
                        }
                    } else {
                        // PARA UPDATE DE TRAMITE
                        $cliente_registrado = Cliente::find($cliente['cliente_id']);
                        if ($cliente['complemento'] != '') {
                            $cliente = Cliente::where("ci", $cliente['ci'])
                                ->where("complemento", $cliente['complemento'])
                                ->where("id", "!=", $cliente_registrado->id)
                                ->get()->first();
                            if ($cliente) {
                                $duplicado = true;
                            }
                        } else {
                            $cliente = Cliente::where("ci", $cliente['ci'])
                                ->where("id", "!=", $cliente_registrado->id)
                                ->get()->first();
                            if ($cliente) {
                                $duplicado = true;
                            }
                        }
                    }
                }

                if ($duplicado) {
                    $fail("El Nro. de C.I. en la fila " . ($index + 1) . " ya éxiste en nuestros registros.");
                    continue;
                }
            }
        }
    }
}
