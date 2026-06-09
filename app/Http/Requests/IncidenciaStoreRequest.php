<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IncidenciaStoreRequest extends FormRequest
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
            "tipo_activo_id" => "required",
            "modulo" => "required",
            "tipo_falla" => "required",
            "severidad" => "required",
            "prueba" => "required",
            "resultado" => "required",
            "bug" => "required",
            "estado" => "required",
        ];
    }

    public function messages()
    {
        return [
            "tipo_activo_id.required" => "Debes completar este campo",
            "modulo.required" => "Debes completar este campo",
            "tipo_falla.required" => "Debes completar este campo",
            "severidad.required" => "Debes completar este campo",
            "prueba.required" => "Debes completar este campo",
            "resultado.required" => "Debes completar este campo",
            "bug.required" => "Debes completar este campo",
            "estado.required" => "Debes completar este campo",
        ];
    }
}
