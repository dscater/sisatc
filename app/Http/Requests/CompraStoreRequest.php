<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CompraStoreRequest extends FormRequest
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
            "categoria_producto_id" => "required",
            "producto_id" => "required",
            "cantidad" => "required|numeric|min:1",
            "precio_compra" => "required|numeric|decimal:0,2",
            "total" => "required|numeric|decimal:0,2",
        ];
    }

    public function messages()
    {
        return [
            "categoria_producto_id.required" => "Debes completar este campo",
            "producto_id.required" => "Debes completar este campo",
            "cantidad.required" => "Debes ingresar la cantidad",
            "cantidad.numeric" => "Debes ingresar un valor númerico",
            "precio_compra.required" => "Debes ingresar el precio de compra unidad",
            "precio_compra.numeric" => "Debes ingresar un valor númerico",
            "precio_compra.decimal" => "Debes ingresar un valor con hasta 2 decimales",
            "total.required" => "El total no puede estar vacio o en 0",
            "total.numeric" => "Debes ingresar un valor númerico",
            "total.decimal" => "Debes ingresar un valor con hasta 2 decimales",
        ];
    }
}
