<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EntrenamientoStoreRequest extends FormRequest
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
            'archivo' => 'required|file|mimes:csv,xlsx,xls|max:10240'
        ];
    }

    public function messages()
    {
        return [
            'archivo.required' => 'Debes cargar un archivo.',
            'archivo.file' => 'El archivo seleccionado no es válido.',
            'archivo.mimes' => 'Solo se permiten archivos CSV, XLS o XLSX.',
            'archivo.max' => 'El archivo no debe superar los 10 MB.'
        ];
    }
}
