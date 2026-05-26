<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatoMeteorologico extends Model
{
    protected $table = 'datos_meteorologicos';

    protected $fillable = [
        'fecha_hora',
        'temperatura',
        'humedad',
        'velocidad_viento',
        'direccion_viento',
    ];

    //Convierte automaticamente los tipos de datos al recuperarlos de la BD
    protected $casts = [
        'fecha_hora'=> 'datetime',
        'temperatura'=> 'float',
        'humedad'=> 'float',
        'velocidad_viento'=> 'float',
        'direccion_viento'=> 'float',
    ];
}
