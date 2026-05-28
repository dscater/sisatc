<?php

namespace App\Http\Requests;

use App\Rules\ComisionRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ComisionStoreRequest extends FormRequest
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
            "listDespachosConsolidadosData" => ["required", new ComisionRule()]
        ];
    }

    public function messages()
    {
        return [
            "distribuidor_id.requried" => "Debes seleccionar un distribuidor",
            "listDespachosConsolidadosData.requried" => "Debes seleccionar un distribuidor",
        ];
    }
}
