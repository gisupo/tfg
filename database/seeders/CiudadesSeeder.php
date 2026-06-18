<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Ciudad;
class CiudadesSeeder extends Seeder
{
    public function run(): void
    {
        $ciudades = [
            ['nombre' => 'Gandía', 'provincia' => 'Valencia', 'pais' => 'España', 'latitud' => 38.9677, 'longitud' => -0.1827],
            ['nombre' => 'Valencia', 'provincia' => 'Valencia', 'pais' => 'España', 'latitud' => 39.4699, 'longitud' => -0.3763],
            ['nombre' => 'Alicante', 'provincia' => 'Alicante', 'pais' => 'España', 'latitud' => 38.3452, 'longitud' => -0.4815],
            ['nombre' => 'Castellón', 'provincia' => 'Castellón', 'pais' => 'España', 'latitud' => 39.9864, 'longitud' => -0.0513],
            ['nombre' => 'Madrid', 'provincia' => 'Madrid', 'pais' => 'España', 'latitud' => 40.4168, 'longitud' => -3.7038],
            ['nombre' => 'Barcelona', 'provincia' => 'Barcelona', 'pais' => 'España', 'latitud' => 41.3851, 'longitud' => 2.1734],
            ['nombre' => 'Sevilla', 'provincia' => 'Sevilla', 'pais' => 'España', 'latitud' => 37.3891, 'longitud' => -5.9845],
            ['nombre' => 'Bilbao', 'provincia' => 'Vizcaya', 'pais' => 'España', 'latitud' => 43.2630, 'longitud' => -2.9350],
            ['nombre' => 'Zaragoza', 'provincia' => 'Zaragoza', 'pais' => 'España', 'latitud' => 41.6488, 'longitud' => -0.8891],
            ['nombre' => 'Málaga', 'provincia' => 'Málaga', 'pais' => 'España', 'latitud' => 36.7213, 'longitud' => -4.4213],
            ['nombre' => 'Murcia', 'provincia' => 'Murcia', 'pais' => 'España', 'latitud' => 37.9922, 'longitud' => -1.1307],
            ['nombre' => 'Las Palmas', 'provincia' => 'Las Palmas', 'pais' => 'España', 'latitud' => 28.1235, 'longitud' => -15.4363],
            ['nombre' => 'Santiago de Compostela', 'provincia' => 'A Coruña', 'pais' => 'España', 'latitud' => 42.8782, 'longitud' => -8.5448],
            ['nombre' => 'San Sebastián', 'provincia' => 'Guipúzcoa', 'pais' => 'España', 'latitud' => 43.3183, 'longitud' => -1.9812],
        ];
        foreach ($ciudades as $ciudad) {
            Ciudad::firstOrCreate(['nombre' => $ciudad['nombre']], $ciudad);
        }
    }
}
