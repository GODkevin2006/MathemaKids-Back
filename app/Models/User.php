<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $primaryKey = 'id';
    protected $table = 'usuario';

    protected $fillable = [
        'nombres',
        'apellidos',
        'correo',
        'contraseña',
    ];

 public function rol()
 {
    return $this->belongTo(rol::class,'rol','id');
 }
}
