<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
   
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombres',
        'apellidos',
        'correo',
        'contraseña',
        'id_rol'
    ];

 public function rol()
 {
    return $this->belongTo(Rol::class,'id_rol','id_rol');
    return $this->hasMany(Proyecto::class,'id_usuario','id_usuario'); 
 }
}
