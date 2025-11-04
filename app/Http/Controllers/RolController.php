<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolRequest;
use Illuminate\Http\Request;
use App\Services\RolService;


class RolController extends Controller
{
   
    protected RolService $servicioRol;


    public function __construct(){
        return $this->servicioRol = new RolService;
    }

    public function index()
    {
        try {
            $rol = $this->servicioRol::listarRoles();

            return response()->json([
                'success' => 'Se listaron correctamente',
                'data' =>$rol
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo listar los usuarios',
                'data' =>$e
            ],400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RolRequest $registro)
    {
         try {
            $rolRegistrado = $this->servicioRol::crearRol($registro->validated());

            return response()->json([
                'succes' => 'El rol se registró correctamente',
                'data' => $rolRegistrado,
            ],201);
        
        } catch (\Exception $e) {
            return response()->json ([
                'error' => 'El rol no se pudo registrar',
                'data' => $e
            ],400);
            
        } 

         
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
         try {
            $rol = $this->servicioRol::obtenerRol($id);

            if(!$rol) {

                return response()->json([
                'Error' => 'El rol no se pudo encontrar',
                
            ],404);

            }

            return response()->json([
                'success' => 'El rol se encontró correctamente',
                'data' =>$rol
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo encontrar el rol',
                'data' =>$e
            ],400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RolRequest $camposActualizados, $id_rol)
    {
         try {
            $rol = $this->servicioRol::actualizarRol($camposActualizados->validated(),$id_rol);

            if(!$rol) {

                return response()->json([
                'Error' => 'El rol no se pudo encontrar',
                
            ],404);

            }

            return response()->json([
                'success' => 'El rol se actualizó correctamente',
                'data' =>$rol
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo actualizar el rol',
                'data' =>$e
            ],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_rol)
    
    {
         try {
            $rol = $this->servicioRol::eliminarRol($id_rol);

            if(!$rol) {

                return response()->json([
                'Error' => 'El rol no se pudo encontrar',
                
            ],404);

            }

            return response()->json([
                'success' => 'El rol se eliminó correctamente',
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo eliminar el rol',
                'data' =>$e
            ],400);
        }
    }
}