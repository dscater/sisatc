<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PresentacionProductoUpdateRequest extends FormRequest
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
            "producto_id" => "required",
            "nombre" => "required",
            "equivale" => "required",
            "precio" => "required",
            "comi_distribuidor" => "required|numeric|between:0,100",
            "comi_vendedor" => "required|numeric|between:0,100",
        ];
    }

    public function messages()
    {
        return [
            "producto_id.required" => "Debes completar este campo",
            "nombre.required" => "Debes completar este campo",
            "nombre.unique" => "Este nombre no esta disponible",
            "equivale.required" => "Debes completar este campo",
            "precio.required" => "Debes completar este campo",
            "comi_distribuidor.required" => "Debes completar este campo",
            "comi_distribuidor.numeric" => "Debes ingresar un valor númerico",
            "comi_distribuidor.between" => "Debes ingresar un valor entre 0 y 100",
            "comi_vendedor.required" => "Debes completar este campo",
            "comi_vendedor.numeric" => "Debes ingresar un valor númerico",
            "comi_vendedor.between" => "Debes ingresar un valor entre 0 y 100",
        ];
    }
}
