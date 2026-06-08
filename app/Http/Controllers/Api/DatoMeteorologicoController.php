<?php

namespace App\Http\Controllers\Api;

use App\Models\DatoMeteorologico;
use App\Models\Ciudad;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

class DatoMeteorologicoController extends Controller
{
    //Devuelve todos los registros con el nombre de la ciudad.
    public function index(Request $request)
    {
        $datosMeteorologicos = DatoMeteorologico::with('ciudad')
            ->orderBy('fecha_hora', 'desc')->get();

        $datosMeteorologicos = $datosMeteorologicos->map(function ($dato) {
            return [
                'id'               => $dato->id,
                'ciudad_id'        => $dato->ciudad_id,
                'ciudad'           => $dato->ciudad ? $dato->ciudad->nombre : 'Sin ciudad',
                'fecha_hora'       => $dato->fecha_hora,
                'temperatura'      => $dato->temperatura,
                'humedad'          => $dato->humedad,
                'velocidad_viento' => $dato->velocidad_viento,
                'direccion_viento' => $dato->direccion_viento,
            ];
        });
        return response()->json($datosMeteorologicos, 200);
    }

    //Devuelve un único registro por su ID.
    public function show(DatoMeteorologico $datoMeteorologico)
    {
        return response()->json([
            'id'               => $datoMeteorologico->id,
            'ciudad_id'        => $datoMeteorologico->ciudad_id,
            'ciudad'           => $datoMeteorologico->ciudad ? $datoMeteorologico->ciudad->nombre : 'Sin ciudad',
            'fecha_hora'       => $datoMeteorologico->fecha_hora,
            'temperatura'      => $datoMeteorologico->temperatura,
            'humedad'          => $datoMeteorologico->humedad,
            'velocidad_viento' => $datoMeteorologico->velocidad_viento,
            'direccion_viento' => $datoMeteorologico->direccion_viento,
        ], 200);
    }

    //Devuelve los últimos 50 registros de una ciudad
    public function porCiudad(Ciudad $ciudad)
    {
        $datos = DatoMeteorologico::where('ciudad_id', $ciudad->id)
            ->orderBy('fecha_hora', 'desc')->limit(50)->get();

        $datos = $datos->map(function ($dato) use ($ciudad) {
            return [
                'id'               => $dato->id,
                'ciudad_id'        => $dato->ciudad_id,
                'ciudad'           => $ciudad->nombre,
                'fecha_hora'       => $dato->fecha_hora,
                'temperatura'      => $dato->temperatura,
                'humedad'          => $dato->humedad,
                'velocidad_viento' => $dato->velocidad_viento,
                'direccion_viento' => $dato->direccion_viento,
            ];
        });
        return response()->json($datos, 200);
    }

    /**
     * Devuelve estadísticas globales de todos los registros.
     * Usamos los métodos de Colecciones de Laravel (max, min, avg, count).
     */
    public function estadisticas()
    {
        $datos = DatoMeteorologico::all();

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

    //Devuelve la lista de ciudades para rellenar el selector del frontend.
    public function ciudades()
    {
        $ciudades = Ciudad::orderBy('nombre', 'asc')->get(['id', 'nombre', 'provincia']);
        return response()->json($ciudades, 200);
    }

    //Ejecuta la ETL desde el botón del frontend.
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