<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoriaRequest extends FormRequest
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
            'nombre_categoria' => ($isUpdate ? 'sometimes' : 'required').'|string|max:30|min:1',
            'orden_publicacion' => ($isUpdate ? 'sometimes' : 'required'). '|integer|max:30|min:1',
            'estado'  => ($isUpdate ? 'sometimes' : 'required') . '|in:activo,inactivo',
        ];
    
    }
    public function meesage(): array
    {
    return [
        // Nombre
        'nombre_categoria.required' => 'El campo nombres es obligatorio.',
        'nombre_categoria.string' => 'El campo nombres debe contener solo texto.',
        'nombre_categoria.min' => 'El nombre debe tener al menos 10 caracteres.',
        'nombre_categoria.max' => 'El nombre no puede tener más de 30 caracteres.',


         // Orden publicacion
        'orden_publicacion.required' => 'El campo orden publicacion es obligatorio.',
        'orden_publicacion.string' => 'El campo orden publicacion debe contener solo texto.',
        'orden_publicacion.min' => 'El orden publicacion debe tener al menos 10 caracteres.',
        'orden_publicacion.max' => 'El orden publicacion no puede tener más de 30 caracteres.',

    ];
    }
}