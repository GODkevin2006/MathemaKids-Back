<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProyectoRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        // Permitimos todas las solicitudes por ahora
        return true;
    }

    /**
     * Reglas de validación para la solicitud.
     */
    public function rules(): array
    {
        return [
            'id_usuario' => 'required|integer|exists:usuario,id_usuario',
            'nombre' => 'required|string|max:50',
            'descripcion' => 'nullable|string',
            'imagen_portada' => 'nullable|string|max:255',
        ];
    }

    /**
     * Mensajes personalizados para los errores de validación.
     */
    public function messages(): array
    {
        return [
            'id_usuario.required' => 'El usuario es obligatorio.',
            'id_usuario.exists' => 'El usuario no existe en la base de datos.',
            'nombre.required' => 'El nombre del proyecto es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 50 caracteres.',
        ];
    }
}
