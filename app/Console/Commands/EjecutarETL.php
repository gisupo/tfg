<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\DatoMeteorologico;
use Carbon\Carbon;
use Exception;

class EjecutarETL extends Command
{
    //Para ejecutar la ETL(php artisan etl:ejecutar)
    protected $signature = 'etl:ejecutar';
    protected $description = 'Extrae los datos meteorológicos actuales de Gandia y los guarda en la base de datos.';

    public function handle()
    {
        $this->info('Iniciando ETL...');

      //1. EXTRACCIÓN (Extract)
      //Hacemos la petición HTTP a la API externa de Open-Meteo
        $respuesta  = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude'          =>  38.97,
            'longitude'         =>  -0.18,
            'current_weather'   =>  true,
            'hourly'            =>  'relative_humidity_2m',
            'timezone'          =>  'Europe/Madrid',
        ]); 

        //Si la API falla (no devuelve un estado 200), se para el programa.
        if (!$respuesta->ok()){
            $this->error('Error: No se pudo conectar con la API');
            return Command::FAILURE;//Equivale a retornar 1 (error)
        }
        //Convertimos la respuesta JSON en un array de PHP
        $datos = $respuesta->json();


        //2. TRANSFORMACIÓN (Transform)
        //Extraemos las partes que nos interesan de JSON
        $tiempoActual = $datos['current_weather'];
        $humedadHora = $datos['hourly']['relative_humidity_2m'][0];

        //Limpiamos los datos pasándolos a decimales (float) y redondeando
        $temperatura        = round((float) $tiempoActual['temperature'], 2);
        $velocidadViento    = round((float) $tiempoActual['windspeed'], 2);
        $direccionViento    = round((float) $tiempoActual['winddirection'], 2);
        $humedad            = round((float) $humedadHora, 2);

        //Validaciones básicas para asegurar que no entran datos absurdos a la BD
        if ($temperatura < -50 || $temperatura > 60) {
            $this->error("Validación fallida: Temperatura irreal ($temperatura °C)");
            Command::FAILURE;
        }

        //3. CARGA (Load)
        //Guardamos los datos limpios en la BD usando el Modelo de Eloquent
        DatoMeteorologico::create([
            'fecha_hora'        =>Carbon::now('Europe/Madrid'),
            'temperatura'       =>$temperatura,
            'humedad'           =>$humedad,
            'velocidad_viento'  =>$velocidadViento,
            'direccion_viento'  =>$direccionViento,   
        ]);

        //Mensaje final de éxito con el resumen de lo guardado
        $this->info('Datos guardados correctamente en la BD.');
        $this->line("-> Temperatura: $temperatura °C");
        $this->line("-> Humedad: $humedad %");
        $this->line("-> Viento: $velocidadViento km/h");
        $this->line("-> dirección: $direccionViento °");

        return Command::SUCCESS;//equivale a retornar 0 (éxito)
    }
}
