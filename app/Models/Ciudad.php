<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'ciudades';
    protected $fillable = [
        'nombre',
        'provincia',
        'pais',
        'latitud',
        'longitud',
    ];
    //convierte automaticamente los tipos de datos al recuperarlos de la BD
    protected $casts = [
        'latitud' => 'float',
        'longitud' => 'float',
    ];

    //Una ciudad tiene muchos datos meteorológicos
    public function datosMeteorologicos(){
        return $this->hasMany(DatoMeteorologico::class, 'ciudad_id');
    }
}
