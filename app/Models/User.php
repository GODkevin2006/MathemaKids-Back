<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    protected $hidden = [
        'contraseña',
    ];

    protected $fillable = [
        'nombres',
        'apellidos',
        'correo',
        'contraseña',
        'estado',
        'id_rol'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // Campo que actúa como "email" para login
    public function username()
    {
        return 'correo';
    }

    // Campo que actúa como "password"
    public function getAuthPassword()
    {
        return $this->contraseña;
    }
}
