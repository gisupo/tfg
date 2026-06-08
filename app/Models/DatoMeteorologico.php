<?php

namespace App\Models;
use App\Models\Ciudad;
use Illuminate\Database\Eloquent\Model;


class DatoMeteorologico extends Model
{
    protected $table = 'datos_meteorologicos';

    protected $fillable = [
        'ciudad_id',
        'fecha_hora',
        'temperatura',
        'humedad',
        'velocidad_viento',
        'direccion_viento',
    ];

    //Convierte automaticamente los tipos de datos
    protected $casts = [
        'fecha_hora' => 'datetime',
        'temperatura' => 'float',
        'humedad' => 'float',
        'velocidad_viento' => 'float',
        'direccion_viento' => 'float',
    ];

    //Relación un registro pertenece a una ciudad
    public function ciudad(){
        return $this->belongsTo(Ciudad::class);
    }
}
