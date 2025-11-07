<?php

namespace App\Services;

use App\Models\Proyecto;

class ProyectoService
{
    public static function crearProyecto($registroProyecto)
    {
        return Proyecto::create($registroProyecto);
    }

    public static function listarProyecto()
    {
        return Proyecto::get();
    }

    public static function obtenerProyecto($id_proyecto)
    {
        $proyecto = Proyecto::find($id_proyecto);

        if (!$proyecto) {
            return null;
        }

        return $proyecto;
    }

    public static function actualizarProyecto($camposActualizados, $id_proyecto)
    {
        $proyecto = Proyecto::find($id_proyecto);

        if (!$proyecto) {
            return null;
        }

        $proyecto->update($camposActualizados);

        return $proyecto->fresh();
    }

    public static function eliminarProyecto($id_proyecto)
    {
        $proyecto = Proyecto::find($id_proyecto);

        if (!$proyecto) {
            return null;
        }

        $proyecto->estado = $proyecto->estado === 'activo' ? 'inactivo' : 'activo';
        $proyecto->save();

        return true;
    }
}
