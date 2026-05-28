<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
            "nombre" => "required|min:2",
            "apellido" => "required|min:2",
            "email" => "required|email|unique:users,email",
            "fono" => "required",
            "acceso" => "required",
            "password" => "required|min:6",
            "tipo" => "required",
            "foto" => "nullable",
        ];
    }

    /**
     * Mensages validacion
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            "usuario.required" => "Este campo es obligatorio",
            "usuario.min" => "Debes ingresar al menos :min caracteres",
            "usuario.unique" => "Este usuario no esta disponible",
            "nombre.required" => "Este campo es obligatorio",
            "nombre.min" => "Debes ingresar al menos :min caracteres",
            "apellido.required" => "Este campo es obligatorio",
            "apellido.min" => "Debes ingresar al menos :min caracteres",
            "email.required" => "Este campo es obligatorio",
            "email.email" => "Debes ingresar un correo valido",
            "email.unique" => "Este correo no esta disponible",
            "fono.required" => "Este campo es obligatorio",
            "fono.min" => "Debes ingresar al menos :min caracteres",
            "acceso.required" => "Este campo es obligatorio",
            "bloqueo.required" => "Este campo es obligatorio",
            "tipo.required" => "Este campo es obligatorio",
        ];
    }
}
