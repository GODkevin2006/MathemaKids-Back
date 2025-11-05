<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProyectoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH']);

        $rules = [
            'nombre' => ($isUpdate ? 'sometimes' : 'required') . '|string|max:50',
            'descripcion' => ($isUpdate ? 'sometimes' : 'required') . '|string|max:500|min:10',
            'imagen_portada' => ($isUpdate ? 'sometimes' : 'required') . '|string|max:255',
        ];

        // 🔹 Solo exigir id_usuario cuando se crea (POST)
        if ($this->isMethod('post')) {
            $rules['id_usuario'] = 'required|integer|exists:usuario,id_usuario';
        }

        return $rules;
    }

    public function messages(): array
{
    return [
        // NOMBRE
        'nombre.required' => 'El nombre del proyecto es obligatorio.',
        'nombre.string' => 'El nombre del proyecto debe ser una cadena de texto.',
        'nombre.max' => 'El nombre del proyecto no puede tener más de 50 caracteres.',

        // DESCRIPCIÓN
        'descripcion.required' => 'La descripción del proyecto es obligatoria.',
        'descripcion.string' => 'La descripción del proyecto debe ser una cadena de texto.',
        'descripcion.max' => 'La descripción no puede tener más de 500 caracteres.',
        'descripcion.min' => 'La descripción debe tener al menos 10 caracteres.',

        // IMAGEN PORTADA
        'imagen_portada.required' => 'La imagen de portada es obligatoria.',
        'imagen_portada.string' => 'La imagen de portada debe ser una cadena de texto (por ejemplo, una URL o ruta).',
        'imagen_portada.max' => 'La imagen de portada no puede tener más de 255 caracteres.',
    ];
}

}
