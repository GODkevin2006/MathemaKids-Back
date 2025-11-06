<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Proyecto extends Model
{
    use HasFactory;

    // Nombre exacto de la tabla
    protected $table = 'proyecto';

    // Nombre de la clave primaria
    protected $primaryKey = 'id_proyecto';

    // Si la tabla tiene timestamps (created_at, updated_at)
    public $timestamps = true;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'id_usuario',
        'nombre',
        'descripcion',
        'imagen_portada',
    ];

    // Relación: un proyecto pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
