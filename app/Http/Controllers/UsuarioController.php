<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use Illuminate\Http\Request;
use App\Services\UserService;


class UsuarioController extends Controller
{
   
    protected UserService $servicioUsuario;


    public function __construct(){
        return $this->servicioUsuario = new UserService;
    }

    public function index()
    {
        try {
            $usuarios = $this->servicioUsuario::listarUsuarios();

            return response()->json([
                'success' => 'Se listaron correctamente',
                'data' =>$usuarios
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
    public function store(UsuarioRequest $registro)
    {
         try {
            $usuarioRegistrado = $this->servicioUsuario::crearUsuario($registro->validated());

            return response()->json([
                'succes' => 'El usuario se registró correctamente',
                'data' => $usuarioRegistrado,
            ],201);
        
        } catch (\Exception $e) {
            return response()->json ([
                'error' => 'El usuario no se pudo registrar',
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
            $usuario = $this->servicioUsuario::obtenerUsuario($id);

            if(!$usuario) {

                return response()->json([
                'Error' => 'El usuario no se pudo encontrar',
                
            ],404);

            }

            return response()->json([
                'success' => 'El usuario se encontró correctamente',
                'data' =>$usuario
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo encontrar el usuario',
                'data' =>$e
            ],400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UsuarioRequest $camposActualizados, $id_usuario)
    {
         try {
            $usuario = $this->servicioUsuario::actualizarUsuario($camposActualizados->validated(),$id_usuario);

            if(!$usuario) {

                return response()->json([
                'Error' => 'El usuario no se pudo encontrar',
                
            ],404);

            }

            return response()->json([
                'success' => 'El usuario se actualizó correctamente',
                'data' =>$usuario
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo actualizar el usuario',
                'data' =>$e
            ],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_usuario)
    
    {
         try {
            $usuario = $this->servicioUsuario::eliminarUsuario($id_usuario);

            if(!$usuario) {

                return response()->json([
                'Error' => 'El usuario no se pudo encontrar',
                
            ],404);

            }

            return response()->json([
                'success' => 'El usuario se eliminó correctamente',
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo eliminar el usuario',
                'data' =>$e
            ],400);
        }
    }
}