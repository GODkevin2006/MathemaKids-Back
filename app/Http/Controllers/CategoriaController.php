<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriaRequest;
use Illuminate\Http\Request;
use App\Services\CategoriaService;


class CategoriaController extends Controller
{
   
    protected CategoriaService $servicioCategoria;


    public function __construct(){
        return $this->servicioCategoria = new CategoriaService;
    }

    public function index()
    {
        try {
            $categorias = $this->servicioCategoria::listarCategoria();

            return response()->json([
                'success' => 'Se listaron correctamente',
                'data' =>$categorias
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo listar las categorias',
                'data' =>$e
            ],400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoriaRequest $registro)
    {
         try {
            $categoriaRegistrado = $this->servicioCategoria::crearCategoria($registro->validated());

            return response()->json([
                'success' => 'La categoria se registró correctamente',
                'data' => $categoriaRegistrado,
            ],201);
        
        } catch (\Exception $e) {
            return response()->json ([
                'error' => 'La categoria no se pudo registrar',
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
            $categoria = $this->servicioCategoria::obtenerCategoria($id);

            if(!$categoria) {

                return response()->json([
                'Error' => 'La categoria no se pudo encontrar',
                
            ],404);

            }

            return response()->json([
                'success' => 'La categoria se encontró correctamente',
                'data' =>$categoria
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo encontrar La categoria',
                'data' =>$e
            ],400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoriaRequest $camposActualizados, $id_categoria)
    {
         try {
            $categoria = $this->servicioCategoria::actualizarCategoria($camposActualizados->validated(),$id_categoria);

            if(!$categoria) {

                return response()->json([
                'Error' => 'La categoria no se pudo encontrar',
                
            ],404);

            }

            return response()->json([
                'success' => 'La categoria se actualizó correctamente',
                'data' =>$categoria
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo actualizar la categoria',
                'data' =>$e
            ],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_categoria)
    
    {
         try {
            $categoria = $this->servicioCategoria::eliminarCategoria($id_categoria);

            if(!$categoria) {

                return response()->json([
                'Error' => 'La categoria no se pudo encontrar',
                
            ],404);

            }

            return response()->json([
                'success' => 'La categoria se eliminó correctamente',
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo eliminar la categoria',
                'data' =>$e
            ],400);
        }
    }
}