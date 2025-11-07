<?php

namespace App\Services;
use App\Models\Categoria;

class CategoriaService{
    public static function crearCategoria($registroCategoria){
        return categoria::create($registroCategoria);
    }


    public static function listarCategoria(){
        return categoria::get();
    }

    public static function obtenerCategoria($id_categoria){
        $categoria = categoria::find($id_categoria);

        if(!$categoria)
        {
            return null;
        }

        return $categoria;
    }

    public static function actualizarCategoria($camposActualizados,$id_categoria){
        $categoria = categoria::find($id_categoria);

        if(!$categoria) {
            return null;
        }

        $categoria->update($camposActualizados);

        return $categoria->fresh();
        }

    public static function eliminarCategoria($id_categoria){
        $categoria = categoria::find($id_categoria);

        if(!$categoria){
            return null;
        }

        $categoria->update(['estado'=>'inactivo']);

        return true;
        }
}