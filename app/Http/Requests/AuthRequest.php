<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
             'correo' => ($isUpdate ? 'sometimes' : 'required').'|email|unique:usuario,correo|min:5|max:100',
            'contraseña' => ($isUpdate ? 'sometimes' : 'required'). '|integer|max:8|min:30',
        ];
    }

    public function messages(): array
{
    return [
        // EMAIL
        'correo.required' => 'El campo correo electrónico es obligatorio.',
        'correo.email' => 'Debe ingresar un correo electrónico válido.',
        'correo.unique' => 'Este correo electrónico ya está registrado.',
        'correo.min' => 'El correo debe tener al menos 5 caracteres.',
        'correo.max' => 'El correo no puede superar los 100 caracteres.',

        // PASSWORD
        'contraseña.required' => 'La contraseña es obligatoria.',
        'contraseña.string' => 'La contraseña debe ser una cadena de texto.',
        'contraseña.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'contraseña.max' => 'La contraseña no puede superar los 30 caracteres.',
    ];
}
}
