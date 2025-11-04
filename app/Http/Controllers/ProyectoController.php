<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Http\Requests\ProyectoRequest;
use Illuminate\Http\JsonResponse;

class ProyectoController extends Controller
{
    // Mostrar todos los proyectos
    public function index()
    {
        $proyectos = Proyecto::all();
        return response()->json($proyectos);
    }

    // Crear un nuevo proyecto
    public function store(ProyectoRequest $request): JsonResponse
    {
        $validated = $request->validate([
            'id_usuario' => 'required|integer',
            'nombre' => 'required|string|max:50',
            'descripcion' => 'nullable|string',
            'imagen_portada' => 'nullable|string|max:255',
        ]);

        $proyecto = Proyecto::create($request->validated());

        return response()->json([
            'message' => 'Proyecto creado correctamente',
            'data' => $proyecto
        ]);
    }

    // Mostrar un proyecto específico
    public function show($id)
    {
        $proyecto = Proyecto::findOrFail($id);
        return response()->json($proyecto);
    }

    // Actualizar un proyecto existente
    public function update(Request $request, $id)
    {
        $proyecto = Proyecto::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:50',
            'descripcion' => 'nullable|string',
            'imagen_portada' => 'nullable|string|max:255',
        ]);

        $proyecto->update($validated);

        return response()->json([
            'message' => 'Proyecto actualizado correctamente',
            'data' => $proyecto
        ]);
    }

    // Eliminar un proyecto
    public function destroy($id)
    {
        $proyecto = Proyecto::findOrFail($id);
        $proyecto->delete();

        return response()->json(['message' => 'Proyecto eliminado correctamente']);
    }
}
