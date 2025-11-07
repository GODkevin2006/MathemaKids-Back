<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicacionRequest extends FormRequest
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
        $isUpdate = in_array($this->method(),['PUT', 'PATCH']);

        return [
            'id_categoria'      => ($isUpdate ? 'sometimes' : 'required') . '|integer|exists:categoria,id_categoria',
            'id_usuario'        => ($isUpdate ? 'sometimes' : 'required') . '|integer|exists:usuario,id_usuario',
            'tipo_publicacion'  => ($isUpdate ? 'sometimes' : 'required') . '|string|in:blog,noticias',
            'titulo'            => ($isUpdate ? 'sometimes' : 'required') . '|string|min:5|max:100',
            'contenido'         => ($isUpdate ? 'sometimes' : 'required') . '|string|min:10',
            'fecha_publicacion' => ($isUpdate ? 'sometimes' : 'required') . '|date',
            'imagen_destacada'  => ($isUpdate ? 'sometimes' : 'required') . '|string|max:255',
            'numero_vistas'     => ($isUpdate ? 'sometimes' : 'nullable') . '|integer|min:0',
            'estado'            => ($isUpdate ? 'sometimes' : 'required') . '|in:activo,inactivo',
        ];
    }

    public function messages(): array
    {
        return [
        // id_categoria
        'id_categoria.required'     => 'La categoría es obligatoria.',
        'id_categoria.exists'       => 'La categoría seleccionada no existe.',
        
        // id_usuario
        'id_usuario.required'       => 'El usuario es obligatorio.',
        'id_usuario.exists'         => 'El usuario seleccionado no existe.',

        // tipo_publicacion
        'tipo_publicacion.required' => 'El tipo de publicación es obligatorio.',
        'tipo_publicacion.in'       => 'El tipo de publicación debe ser "block" o "noticias".',

        // titulo
        'titulo.required'           => 'El título es obligatorio.',
        'titulo.min'                => 'El título debe tener al menos 5 caracteres.',
        'titulo.max'                => 'El título no puede superar los 100 caracteres.',

        // contenido
        'contenido.required'        => 'El contenido es obligatorio.',
        'contenido.min'             => 'El contenido debe tener al menos 10 caracteres.',

        // fecha_publicacion
        'fecha_publicacion.required'  => 'La fecha de publicación es obligatoria.',
        'fecha_publicacion.date'      => 'La fecha de publicación debe tener un formato válido (YYYY-MM-DD).',

        // imagen_destacada
        'imagen_destacada.required' => 'La imagen destacada es obligatoria.',
        'imagen_destacada.string'   => 'La imagen destacada debe ser una cadena de texto válida.',
        'imagen_destacada.max'      => 'La ruta de la imagen no puede superar los 255 caracteres.',

        // vistas
        'numero_vistas.integer'     => 'El número de vistas debe ser un valor numérico entero.',
        'numero_vistas.min'         => 'El número de vistas no puede ser negativo.',

        ];
    }
}
