<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\DatoMeteorologico;
use App\Models\Ciudad;
use Carbon\Carbon;
use Exception;

class EjecutarETL extends Command
{
    //Configuración de la ETL
    protected $signature = 'etl:ejecutar';
    protected $description = 'ETL meteorológica';

    public function handle()
    {
        $this->info('Iniciando ETL...');

        //Obtenemos todas las ciudades que tenemos en la BD
        $ciudades = Ciudad::all();

        if ($ciudades->isEmpty()) {
            $this->error('No hay ciudades en la BD');
            return Command::FAILURE; //Equivale a retornar 1 (error)
        }
        foreach ($ciudades as $ciudad) {

            //1. EXTRACCIÓN (Extract)
            //Hacemos la petición HTTP a la API Open-Meteo
            $respuesta = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $ciudad->latitud,
                'longitude' => $ciudad->longitud,
                'current_weather' => true,
                'hourly' => 'relative_humidity_2m',
                'timezone' => 'Europe/Madrid',
            ]);

            //Usamos continue para saltar a la siguiente ciudad si la API falla.
            if (!$respuesta->ok()) {
                $this->error("Error al conectar con la API {$ciudad->nombre}");
                continue;
            }

            //Convertimos la respuesta JSON en un array
            $datos = $respuesta->json();


            //2. TRANSFORMACIÓN (Transform)
            //Extraemos las partes que nos interesan de JSON
            $tiempoActual = $datos['current_weather'];
            $humedadHora = $datos['hourly']['relative_humidity_2m'][0];

            //Limpiamos los datos pasándolos a decimales y redondeando
            $temperatura = round((float) $tiempoActual['temperature'], 2);
            $velocidadViento = round((float) $tiempoActual['windspeed'], 2);
            $direccionViento = round((float) $tiempoActual['winddirection'], 2);
            $humedad = round((float) $humedadHora, 2);

            //Validaciones para asegurar que no entren datos poco reales
            if ($temperatura < -50 || $temperatura > 60) {
                $this->error("Validación fallida para $ciudad->nombre: Temperatura no válida ($temperatura °C)");
                continue;
            }
            if ($humedad < 0 || $humedad > 100) {
                $this->error("Validación fallida para {$ciudad->nombre}: Humedad no válida ($humedad %) ");
                continue;
            }

            //3. CARGA (Load)
            //Guardamos los datos limpios en la BD usando el Modelo de Eloquent
            DatoMeteorologico::create([
                'ciudad_id' => $ciudad->id,
                'fecha_hora' => Carbon::now('Europe/Madrid'),
                'temperatura' => $temperatura,
                'humedad' => $humedad,
                'velocidad_viento' => $velocidadViento,
                'direccion_viento' => $direccionViento,
            ]);

            //Mensaje de éxito
            $this->info("[OK] Ciudad: {$ciudad->nombre}: {$temperatura}°C | Humedad: {$humedad}% | Velocidad del viento: {$velocidadViento}km/h");
        }
        $this->info('!ETL completada!');
        return Command::SUCCESS; //equivale a retornar 0 (éxito) al terminar todo el bucle
    }
}
