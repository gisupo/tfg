<?php
use App\Models\Ciudad;
use Illuminate\Support\Facades\Route;

Route::get('/meteorologia', function () {
    $ciudades = Ciudad::all();
    return view('meteorologia', compact('ciudades'));
});