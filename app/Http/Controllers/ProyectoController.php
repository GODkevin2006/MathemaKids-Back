<?php
//NO TOCAR HIJOS DE LARRY

namespace App\Http\Controllers;

use App\Http\Requests\ProyectoRequest;
use Illuminate\Http\Request;
use App\Services\ProyectoService;


class ProyectoController extends Controller
{
   
    protected ProyectoService $servicioProyecto;


    public function __construct(){
         $this->servicioProyecto = new ProyectoService;
    }

    public function index()
    {
        try {
            $proyecto = $this->servicioProyecto::listarProyecto();

            return response()->json([
                'success' => 'Se listaron correctamente',
                'data' =>$proyecto
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo listar los proyectos',
                'data' =>$e
            ],400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProyectoRequest $registro)
    {
         try {
            $proyectoRegistrado = $this->servicioProyecto::crearProyecto($registro->validated());

            return response()->json([
                'success' => 'El proyecto se registró correctamente',
                'data' => $proyectoRegistrado,
            ],201);
        
        } catch (\Exception $e) {
            return response()->json ([
                'error' => 'El proyecto no se pudo registrar',
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
            $proyecto = $this->servicioProyecto::obtenerProyecto($id);

            if(!$proyecto) {

                return response()->json([
                'Error' => 'El proyecto no se pudo encontrar',
                
            ],404);

            }

            return response()->json([
                'success' => 'El proyecto se encontró correctamente',
                'data' =>$proyecto
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo encontrar el proyecto',
                'data' =>$e
            ],400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( ProyectoRequest $camposActualizados, $id_proyecto,)

    {
         try {
            $proyecto = $this->servicioProyecto::actualizarProyecto($camposActualizados->validated(),$id_proyecto);

            if(!$proyecto) {

                return response()->json([
                'Error' => 'El proyecto no se pudo encontrar',
                
            ],404);

            }

            return response()->json([
                'success' => 'El proyecto se actualizó correctamente',
                'data' =>$proyecto
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo actualizar el proyecto',
                'data' =>$e
            ],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_proyecto)
    
    {
         try {
            $proyecto = $this->servicioProyecto::eliminarProyecto($id_proyecto);

            if(!$proyecto) {

                return response()->json([
                'Error' => 'El proyecto no se pudo encontrar',
                
            ],404);

            }

            return response()->json([
                'success' => 'El proyecto se eliminó correctamente',
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo eliminar el proyecto',
                'data' =>$e
            ],400);
        }
    }
}
