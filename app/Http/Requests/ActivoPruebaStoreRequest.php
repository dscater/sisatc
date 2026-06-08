<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ActivoPruebaStoreRequest extends FormRequest
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
            "descripcion" => "required",
            "modulo" => "required",
            "prueba" => "required",
        ];
    }

    public function messages()
    {
        return [
            "activo_id.required" => "Debes completar este campo",
            "descripcion.required" => "Debes completar este campo",
            "modulo.required" => "Debes completar este campo",
            "prueba.required" => "Debes completar este campo",
        ];
    }
}
