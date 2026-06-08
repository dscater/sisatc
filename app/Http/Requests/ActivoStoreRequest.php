<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ActivoStoreRequest extends FormRequest
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
            "codigo" => "required",
            "nombre" => "required",
            "descripcion" => "required",
            "tipo_activo_id" => "required",
            "version" => "required",
        ];
    }

    public function messages(): array
    {
        return [
            "codigo.required" => "El campo código es obligatorio.",
            "nombre.required" => "El campo nombre es obligatorio.",
            "descripcion.required" => "El campo descripción es obligatorio.",
            "tipo_activo_id.required" => "El campo tipo de activo es obligatorio.",
            "version.required" => "El campo versión es obligatorio.",
        ];
    }
}
