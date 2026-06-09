<?php

namespace App\Http\Requests;

use App\Rules\EjecucionArchivosRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EjecucionTrazabilidadUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "activo_id" => "required",
            "estado" => "required",
            "ejecucion_archivos" => ["required", new EjecucionArchivosRule()],
            "eliminados_archivos" => "nullable|array"
        ];
    }

    public function messages()
    {
        return [
            "activo_id.required" => "Debes completar este campo",
            "estado.required" => "Debes completar este campo",
            "ejecucion_archivos.required" => "Debes cargar al menos 1 archivo",
        ];
    }
}
