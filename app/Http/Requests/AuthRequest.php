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
        return [
            'correo' => 'required|email',
            'contraseña' => 'required|string',
        ];
    }

    public function messages(): array
{
    return [
        // EMAIL
        'correo.required' => 'El campo correo electrónico es obligatorio.',
        'correo.email' => 'Debe ingresar un correo electrónico válido.',

        // PASSWORD
        'contraseña.required' => 'La contraseña es obligatoria.',
        'contraseña.string' => 'La contraseña debe ser una cadena de texto.',
    ];
}
}
