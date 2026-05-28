<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\PotentiallyTranslatedString;

class ClienteCiComplementoRule implements ValidationRule
{

    protected $complemento;
    protected $ignoreId;

    public function __construct($complemento, $ignoreId = null)
    {
        $this->complemento = $complemento;
        $this->ignoreId = $ignoreId;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $query = DB::table('clientes')
            ->where('ci', $value);

        // Manejo correcto de null o vacío
        if ($this->complemento === null || $this->complemento === '') {
            $query->where(function ($q) {
                $q->whereNull('complemento')
                    ->orWhere('complemento', '');
            });
        } else {
            $query->where('complemento', $this->complemento);
        }

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            if ($this->complemento === null || $this->complemento === '') {
                $fail('El nro. de C.I. ya fue registrado.');
            } else {
                $fail('El nro. de C.I. con complemento ya existe.');
            }
        }
    }
}
