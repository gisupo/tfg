<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DatoMeteorologicoController;

Route::prefix('meteorologia')->group(function () {
    //Maneja todo el listado y el filtrado
    Route::get('/datos', [DatoMeteorologicoController::class, 'index']);

    //Ver un registro por ID
    Route::get('/datos/{datoMeteorologico}', [DatoMeteorologicoController::class, 'show']);

    Route::get('/ciudad/{ciudad}', [DatoMeteorologicoController::class, 'porCiudad']);

    //Estadísticas globales
    Route::get('/estadisticas', [DatoMeteorologicoController::class, 'estadisticas']);

    //Lista de ciudades para el selector
    Route::get('/ciudades', [DatoMeteorologicoController::class, 'ciudades']);

    //Ejecutar ETL 
    Route::post('/etl', [DatoMeteorologicoController::class, 'ejecutarETL']);
});