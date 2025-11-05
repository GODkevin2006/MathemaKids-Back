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
            'nombres' => ($isUpdate ? 'sometimes' : 'required').'|string|max:30|min:10',
            'apellidos' => ($isUpdate ? 'sometimes' : 'required').'|string|max:30|min:10',
            'orden_publicacion' => ($isUpdate ? 'sometimes' : 'required'). '|string|max:30|min:10'
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

         // Orden publicacion
        'orden_publicacion.required' => 'El campo orden publicacion es obligatorio.',
        'orden_publicacion.string' => 'El campo orden publicacion debe contener solo texto.',
        'orden_publicacion.min' => 'El orden publicacion debe tener al menos 10 caracteres.',
        'orden_publicacion.max' => 'El orden publicacion no puede tener más de 30 caracteres.',

    ];
    }
}