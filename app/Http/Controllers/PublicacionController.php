<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PublicacionService;
use App\Http\Requests\PublicacionRequest;


class PublicacionController extends Controller
{

    protected PublicacionService $servicioPublicacion;

    public function __construct()
    {
        return $this->servicioPublicacion = new PublicacionService;
    }

    public function index()
    {
        try {
            $publicacion = $this->servicioPublicacion::listarPublicaciones();

            return response()->json([
                'Success'=>'Se listaron correctamente.',
                'Data'=>$publicacion
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'Error'=>'No se pudo listar las publicaciones.',
                'Data'=>$e
            ], 400);
        }
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PublicacionRequest $registro)
    {
        try {
            $publicacionRegistrada = $this->servicioPublicacion::crearPublicacion($registro->validated());

            return response()->json([
                'Success'=>'La publicacion se registro correctamente.',
                'Data'=>$publicacionRegistrada
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'Error'=>'No se pudo registrar la publicacion.',
                'Data'=>$e
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
         try {
            $publicacion = $this->servicioPublicacion::obtenerPublicacion($id);

            if(!$publicacion) {
                return response()->json([
                    'Error'=>'La publicacion no se encontro'
                ], 404);
            }

            return response()->json([
                'Success'=>'La publicacion se encontro correctamente.',
                'Data'=>$publicacion
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'Error'=>'No se encontro la publicacion.',
                'Data'=>$e
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PublicacionRequest $camposActualizados, $id_publicacion)
    {
        try {
            $publicacion = $this->servicioPublicacion::actualizarPublicacion($camposActualizados->validated(), $id_publicacion);

            if(!$publicacion) {
                return response()->json([
                    'Error'=> 'La publicacion no se pudo encontrar'
                ],404);
            }

            return response()->json([
                'Success'=>'La publicacion se actualizo correctamente.',
                'Data'=>$publicacion
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'Error'=>'No se pudo actualizar la publicacion.',
                'Data'=>$e
            ], 400);
        }
    }

  
    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id_publicacion)
    {
    
        try {
            $publicacion = $this->servicioPublicacion::eliminarPublicacion($id_publicacion);

            if(!$publicacion) {
                return response()->json([
                    'Error'=>'La publicacion no se encontro'
                ],404);            
            }

            return response()->json([
                'Success'=>'La publicacion se elimino correctamente.',
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'Error'=>'No se pudo eliminar la publicacion.',
                'Data'=>$e
            ], 400);
        }
    }
   
}
