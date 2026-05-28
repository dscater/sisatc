<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ConsolidadoStoreRequest extends FormRequest
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
            "distribuidor_id" => "required",
            "despacho_id" => "required",
            "observacion" => "nullable",
            "listCategoriaProductoPedidos" => ["required"]
        ];
    }

    public function messages()
    {
        return [
            "distribuidor_id.requried" => "Debes seleccionar un distribuidor",
            "despacho_id.requried" => "Debes seleccionar un despacho",
            "listCategoriaProductoPedidos.requried" => "Debes seleccionar un distribuidor",
        ];
    }
}
