<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use Illuminate\Http\Request;

Class RollController extends Controller 
{
    public function index ()
    {
        return response()->json(rOLL::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_rol' => '|required|string|max:50',
        ]);

        $rol = Rol::create($request->all());
        return response()->json($rol, 201);
    }

    public function show ($id)
    {
        return response()->json(Roll::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $rol = Rol::findOrFail($id);
        $rol->update($request->all());
        return response()->json($rol);
    }

    public function destroy($id)
    {
        $rol::destroy($id);
        return response()->json($rol, 204);
    }
} 