<?php

namespace App\Http\Controllers\Api;

use App\Models\DatoMeteorologico;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;


class DatoMeteorologicoController extends Controller
{
    /**
     * Endpoint: Obtener todos los registros climatológicos.
     * Sirve para listar el historial completo en el Front-end.
     */
    public function index(){
        //1. Consultamos todos los registros de la tabla mediante Eloquent ORM
        $datosMeteorologicos = DatoMeteorologico::orderBy('fecha_hora', 'desc')->get();

        //2. Iteramos y transformamos la colección para controlar los campos que enviamos a la API
        $datosMeteorologicos = $datosMeteorologicos->map(function($dato) {
            return[
                'id'                => $dato->id,
                'fecha_hora'        => $dato->fecha_hora,
                'temperatura'       => $dato->temperatura,
                'humedad'           => $dato->humedad,
                'velocidad_viento'  => $dato->velocidad_viento,
                'direccion_viento'  => $dato->direccion_viento,
            ];
        });
        //3. Devolvemos la respuesta estructurada en formato JSON con código de estado HTTP 200 (OK)
        return response()->json($datosMeteorologicos, 200);
    }
    
    /**
     * Endpoint: Obtener un único registro por su ID.
     * Usa 'Route Model Binding' (Inyección automática del Modelo basado en el ID de la URL).
     */
    public function show(DatoMeteorologico $datoMeteorologico){
        //Retornamos directamente el registro seleccionado en formato JSON
        //Si el ID no existiera, Laravel respondería automáticamente con un error 404 (Not Found)
        return response()->json([
                'id'                => $datoMeteorologico->id,
                'fecha_hora'        => $datoMeteorologico->fecha_hora,
                'temperatura'       => $datoMeteorologico->temperatura,
                'humedad'           => $datoMeteorologico->humedad,
                'velocidad_viento'  => $datoMeteorologico->velocidad_viento,
                'direccion_viento'  => $datoMeteorologico->direccion_viento,
        ], 200);

    }

    /**
     * Endpoint: Obtener cálculos estadísticos globales.
     * Procesa los datos en el servidor en lugar de delegar el cálculo al Front-end.
     */
    public function estadisticas(){
        //1. Recuperamos toda la información meteorológica disponible
        $datos = DatoMeteorologico::all();

        // 2. Usamos los métodos nativos de las Colecciones de Laravel (max, min, avg, count)
        //Esto optimiza el rendimiento evitando bucles foreach manuales o consultas pesadas a la BD
            return response()->json([
            'temp_max'        => $datos->max('temperatura'),
            'temp_min'        => $datos->min('temperatura'),
            'temp_media'      => round($datos->avg('temperatura'), 2),
            'humedad_max'     => $datos->max('humedad'),
            'humedad_min'     => $datos->min('humedad'),
            'humedad_media'   => round($datos->avg('humedad'), 2),
            'viento_max'      => $datos->max('velocidad_viento'),
            'viento_min'      => $datos->min('velocidad_viento'),
            'viento_medio'    => round($datos->avg('velocidad_viento'), 2),
            'total_registros' => $datos->count(),
        ], 200);
    }

    public function ejecutarETL()
{
    try {
        Artisan::call('etl:ejecutar');
        return response()->json(['mensaje' => 'ETL ejecutada correctamente ✅'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}
