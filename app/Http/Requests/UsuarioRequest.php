<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioRequest extends FormRequest
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
        $isUpdate = in_array($this->method(), ['PUT','PATCH']);
        return [
            'nombres' => ($isUpdate ? 'sometimes' : 'required').'|string|max:30|min:10',
            'apellidos' => ($isUpdate ? 'sometimes' : 'required').'|string|max:30|min:10',
            'correo' => ($isUpdate ? 'sometimes' : 'required'). '|email|unique:usuario,correo',
            'contraseña' => ($isUpdate ? 'sometimes' : 'required'). '|min:8|max:20|confirmed',
            'id_rol' => 'sometimes|exists:rol,id_rol'
        ];
    
    }

    public function meesage(): array
    {
    return [
        // Nombres
        'nombres.required' => 'El campo nombres es obligatorio.',
        'nombres.string' => 'El campo nombres debe contener solo texto.',
        'nombres.min' => 'El nombre debe tener al menos 10 caracteres.',
        'nombres.max' => 'El nombre no puede tener más de 30 caracteres.',

        // Apellidos
        'apellidos.required' => 'El campo apellidos es obligatorio.',
        'apellidos.string' => 'El campo apellidos debe contener solo texto.',
        'apellidos.min' => 'El apellido debe tener al menos 10 caracteres.',
        'apellidos.max' => 'El apellido no puede tener más de 30 caracteres.',

        // Correo
        'correo.required' => 'El campo correo es obligatorio.',
        'correo.email' => 'Debe ingresar un correo electrónico válido.',
        'correo.unique' => 'El correo ingresado ya está registrado.',

        // Contraseña
        'contraseña.required' => 'El campo contraseña es obligatorio.',
        'contraseña.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'contraseña.max' => 'La contraseña no puede tener más de 20 caracteres.',
        'contraseña.confirmed' => 'La confirmación de la contraseña no coincide.',
    ];
    }
}
