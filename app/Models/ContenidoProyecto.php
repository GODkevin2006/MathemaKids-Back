<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContenidoProyecto extends Model
{
   
    protected $table = 'contenido_proyecto';
    protected $primaryKey = 'id_contenido';
    public $timestamps = true;

    protected $fillable = [
        'id_proyecto',
        'contenido',
        'fecha_creacion',
        'fecha_actualizacion',
        'archivo_adjunto',
        'estado'
    ];

 public function proyecto()
 {
    return $this->belongTo(Proyecto::class,'id_proyecto','id_proyecto');
 }
}