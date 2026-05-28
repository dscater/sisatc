<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductoUpdateRequest extends FormRequest
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
            "nombre" => "required|unique:productos,nombre," . $this->producto->id,
            "descripcion" => "required",
            "categoria_producto_id" => "required",
            "stock_min" => "required",
            "estado" => "required",
            "imagen" => "nullable",
        ];
    }

    public function messages()
    {
        return [
            "nombre.required" => "Debes completar este campo",
            "descripcion.required" => "Debes completar este campo",
            "categoria_producto_id.required" => "Debes completar este campo",
            "stock_min.required" => "Debes completar este campo",
            "stock_actual.required" => "Debes completar este campo",
            "estado.required" => "Debes completar este campo",
            "imagen.required" => "Debes completar este campo",
        ];
    }
}
