<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContenidoProyectoRequest;
use Illuminate\Http\Request;
use App\Services\ContenidoProyectoService;


class ContenidoProyectoController extends Controller
{
   
    protected ContenidoProyectoService $servicioContenidoProyecto;


    public function __construct(){
        return $this->servicioContenidoProyecto = new ContenidoProyectoService;
    }

    public function index()
    {
        try {
            $contenidoproyecto = $this->servicioContenidoProyecto::listarContenidoProyecto();

            return response()->json([
                'success' => 'Se listaron correctamente',
                'data' =>$contenidoproyecto
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo listar los contenidos',
                'data' =>$e
            ],400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContenidoProyectoRequest $registro)
    {
         try {
            $ContenidoProyectoRegistrado = $this->servicioContenidoProyecto::crearContenidoProyecto($registro->validated());

            return response()->json([
                'success' => 'El contenido se registró correctamente',
                'data' => $ContenidoProyectoRegistrado,
            ],201);
        
        } catch (\Exception $e) {
            return response()->json ([
                'error' => 'El contenido no se pudo registrar',
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
            $contenidoproyecto= $this->servicioContenidoProyecto::obtenerContenidoProyecto($id);

            if(!$contenidoproyecto) {

                return response()->json([
                'Error' => 'El contenido no se pudo encontrar',
                
            ],404);

            }

            return response()->json([
                'success' => 'El contenido se encontró correctamente',
                'data' =>$contenidoproyecto
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo encontrar el contenido',
                'data' =>$e
            ],400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContenidoProyectoRequest $camposActualizados, $id_contenido)
    {
         try {
            $contenidoproyecto = $this->servicioContenidoProyecto::actualizarContenidoProyecto($camposActualizados->validated(),$id_contenido);

            if(!$contenidoproyecto) {

                return response()->json([
                'Error' => 'El contenido no se pudo encontrar',
                
            ],404);

            }

            return response()->json([
                'success' => 'El contenido se actualizó correctamente',
                'data' =>$contenidoproyecto
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo actualizar el contenido',
                'data' =>$e
            ],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_contenido)
    
    {
         try {
            $contenidoproyecto = $this->servicioContenidoProyecto::eliminarContenidoProyecto
            ($id_contenido);

            if(!$contenidoproyecto) {

                return response()->json([
                'Error' => 'El contenido no se pudo encontrar',
                
            ],404);

            }

            return response()->json([
                'success' => 'El contenido se eliminó correctamente',
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo eliminar el contenido',
                'data' =>$e
            ],400);
        }
    }
}