<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RolRequest extends FormRequest
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
            'nombre_rol' => ($isUpdate ? 'sometimes' : 'required').'|string|unique:rol|max:30|min:5',
            'estado'  => ($isUpdate ? 'sometimes' : 'required') . '|in:activo,inactivo',
        ];
    
    }

     public function messages(): array
    {
        return [
            'nombre_rol.required' => 'El nombre del rol es obligatorio.',
            'nombre_rol.string' => 'El nombre del rol debe ser una cadena de texto.',
            'nombre_rol.max' => 'El nombre del rol no puede tener más de 30 caracteres.',
            'nombre_rol.unique' => 'Este nombre de rol ya existe.',
        ];
    }
}
