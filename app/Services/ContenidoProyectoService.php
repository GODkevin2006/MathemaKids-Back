<?php

namespace App\Services;
use App\Models\ContenidoProyecto;

class ContenidoProyectoService{
    public static function crearContenidoProyecto($registroContenidoProyecto){
        return contenidoproyecto::create($registroContenidoProyecto);
    }


    public static function listarContenidoProyecto(){
        return contenidoproyecto::get();
    }

    public static function obtenerContenidoProyecto($id_contenido){
        $contenidoproyecto = contenidoproyecto::find($id_contenido);

        if(!$contenidoproyecto)
        {
            return null;
        }

        return $contenidoproyecto;
    }

    public static function actualizarContenidoProyecto($camposActualizados,$id_contenido){
        $contenidoproyecto = contenidoproyecto::find($id_contenido);

        if(!$contenidoproyecto) {
            return null;
        }

        $contenidoproyecto->update($camposActualizados);

        return $contenidoproyecto->fresh();
        }

    public static function eliminarContenidoProyecto($id_contenido){
        $contenidoproyecto = contenidoproyecto::find($id_contenido);

        if(!$contenidoproyecto){
            return null;
        }

         //para que cuando yo haga un delete no me borre los datos y solo cambie el campo de estado a inactivo
        $contenidoproyecto->estado = $contenidoproyecto->estado === 'activo' ? 'inactivo' : 'activo';
        $contenidoproyecto->save();
        return true;

        }
}