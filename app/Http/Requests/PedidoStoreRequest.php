<?php

namespace App\Http\Requests;

use App\Rules\PedidoDetalleRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PedidoStoreRequest extends FormRequest
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
            "cliente_id" => "required",
            "subtotal" => "required",
            "descuento" => "required",
            "total" => "required",
            "observacion" => "nullable",
            "pedido_detalles" => ["required", new PedidoDetalleRule()],
            "eliminados" => "nullable",
        ];
    }

    public function messages()
    {
        return [
            "cliente_id.required" => "Debes completar este campo",
            "subtotal.required" => "Debes completar este campo",
            "descuento.required" => "Debes completar este campo",
            "total.required" => "Debes completar este campo",
            "observacion.required" => "Debes completar este campo",
            "pedido_detalles.required" => "Debes completar este campo",
        ];
    }
}
