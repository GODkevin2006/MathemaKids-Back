<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categoria extends Authenticatable
{
   
    protected $table = 'categoria';
    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'nombres',
        'apellidos',
        'orden_publicacion',
    ];

 public function publicacion()
 {
    return $this->belongTo(publicacion::class,'id_publicacion','id_publicacion');
 }
}