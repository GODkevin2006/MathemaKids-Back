<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Foundation\Auth\User as AuthUser;
use User as GlobalUser;

class Publicacion extends Model
{
    protected $primaryKey = 'id_publicacion';
    protected $table = 'publicacion';

    protected $fillable = [
        'id_categoria',
        'id_usuario',
        'tipo_publicacion',
        'titulo',
        'contenido',
        'fecha_publicacion',
        'imagen_destacada',
        'numero_vistas',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'id_usuario', 'id_usuario');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class,'id_categoria', 'id_categoria');
    }

}
