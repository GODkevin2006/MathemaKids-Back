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
        'fecha_publicada',
        'imagen_destacada',
        'vistas',
    ];

    public function user()
    {
        return $this->belongsTo(AuthUser::class,'id_usuario', 'id_usuario');
    }
}
