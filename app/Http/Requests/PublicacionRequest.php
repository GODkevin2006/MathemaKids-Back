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
            'id_usuario'        => ($isUpdate ? 'sometimes' : 'required') . '|integer|exists:users,id_usuario',
            'tipo_publicacion'  => ($isUpdate ? 'sometimes' : 'required') . '|string|max:50',
            'titulo'            => ($isUpdate ? 'sometimes' : 'required') . '|string|min:5|max:100',
            'contenido'         => ($isUpdate ? 'sometimes' : 'required') . '|string|min:10',
            'fecha_publicada'   => 'nullable|date',
            'imagen_destacada'  => 'nullable|string|max:255',
            'vistas'            => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            //id_categoria
            'id_categoria.required'     => 'La categoría es obligatoria.',
            'id_categoria.exists'       => 'La categoría seleccionada no existe.',
            
            //id_usuario
            'id_usuario.required'       => 'El usuario es obligatorio.',
            'id_usuario.exists'         => 'El usuario seleccionado no existe.',

            //tipo publicacion requerida
            'tipo_publicacion.required' => 'El tipo de publicación es obligatorio.',

            //titulo
            'titulo.required'           => 'El título es obligatorio.',
            'titulo.min'                => 'El título debe tener al menos 5 caracteres.',

            //contenido
            'contenido.required'        => 'El contenido es obligatorio.',
            'contenido.min'             => 'El contenido debe tener al menos 10 caracteres.',

        ];
    }
}
