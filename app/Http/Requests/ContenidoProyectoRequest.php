<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContenidoProyectoRequest extends FormRequest
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
            'id_proyecto' => ($isUpdate ? 'sometimes' : 'required') . '|integer|exists:proyecto,id_proyecto',
            'contenido' => ($isUpdate ? 'sometimes' : 'required').'|string|min:10',
            'fecha_creacion' => ($isUpdate ? 'sometimes' : 'required'). '|date',
            'fecha_actualizacion' => ($isUpdate ? 'sometimes' : 'required'). '|date',
            'archivo_adjunto' => ($isUpdate ? 'sometimes' : 'required'). '|string|max:255'
        ];
    
    }
    public function messages(): array
    {
        return [
            'contenido.required' => 'El campo contenido es obligatorio.',
            'contenido.string' => 'El contenido debe ser una cadena de texto.',
            'contenido.min' => 'El contenido debe tener al menos 10 caracteres.',

            'fecha_creacion.required' => 'La fecha de creación es obligatoria.',
            'fecha_creacion.date' => 'La fecha de creación debe tener un formato válido.',

            'fecha_actualizacion.required' => 'La fecha de actualización es obligatoria.',
            'fecha_actualizacion.date' => 'La fecha de actualización debe tener un formato válido.',

            'archivo_adjunto.required' => 'El archivo adjunto es obligatorio.',
            'archivo_adjunto.string' => 'El archivo adjunto debe ser texto (ruta o nombre).',
            'archivo_adjunto.max' => 'El archivo adjunto no puede superar los 255 caracteres.',
        ];
    }
}