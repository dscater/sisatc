<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SegmentacionZonaStoreRequest extends FormRequest
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
            "zona" => "required|unique:segmentacion_zonas,zona",
            "departamento_id" => "required",
            "provincia_id" => "required",
            "ciudad_id" => "required",
            "color" => "required",
            "segmentacion" => "required",
        ];
    }

    public function messages()
    {
        return [
            "zona.required" => "Debes completar este campo",
            "zona.unique" => "Esta zona no esta disponible",
            "departamento_id" => "Debes completar este campo",
            "provincia_id" => "Debes completar este campo",
            "ciudad_id" => "Debes completar este campo",
            "color" => "Debes completar este campo",
            "segmentacion" => "Debes dibujar la segmentacion de la zona",
        ];
    }
}
