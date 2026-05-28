<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfiguracionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "nombre_sistema" => "required",
            "alias" => "required",
            "logo" => "nullable",
        ];
    }

    /**
     * Mensajes validacion
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            "nombre_sistema.required" => "Debes completar este campo",
            "alias.required" => "Debes completar este campo",
            "razon_social.required" => "Debes completar este campo",
            "logo.required" => "Debes completar este campo",
            "logo2.required" => "Debes completar este campo",
            "fono.required" => "Debes completar este campo",
            "dir.required" => "Debes completar este campo",
        ];
    }
}
