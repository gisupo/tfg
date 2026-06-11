<?php
use App\Models\Ciudad;
use Illuminate\Support\Facades\Route;

Route::get('/meteorologia', function () {
    try {
        $ciudades = \App\Models\Ciudad::all();
        return response()->json($ciudades);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ]);
    }
});