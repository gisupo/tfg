<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DatoMeteorologicoController;


Route::prefix('meteorologia')->group(function () {

    Route::get('/datos', [DatoMeteorologicoController::class, 'index']);

    Route::get('/datos/{datoMeteorologico}', [DatoMeteorologicoController::class, 'show']);

    Route::get('/estadisticas', [DatoMeteorologicoController::class, 'estadisticas']);

    Route::post('/etl', [DatoMeteorologicoController::class, 'ejecutarETL']);
});
