<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meteo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <nav class="navbar navbar-dark">
        <div class="container">
            <span class="navbar-brand">🌤️ Meteo</span>
            <span class="text-muted small">Datos meteorológicos en tiempo real</span>
            <div class="d-flex gap-2 align-items-center">
                <select id="selectorCiudad" class="form-select form-select-sm" onchange="cambiarCiudad()">
                    <option value="" selected disabled>Cambia de ciudad</option>
                    @foreach ($ciudades->sortBy('nombre') as $ciudad)
                        <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }} ({{ $ciudad->provincia }})</option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-sm" onclick="ejecutarETL()">
                    <i class="fas fa-rotate me-1"></i> Actualizar
                </button>
            </div>
        </div>
    </nav>

    <div class="container my-4">

        <div id="mensaje" class="alert alert-success d-none" role="alert">
            <i class="fas fa-circle-check me-2"></i> Datos actualizados correctamente
        </div>

        <h5 id="tituloCiudad" class="mb-3 fw-bold" style="color: #1a3a5c;">Gandía (Valencia)</h5>

        <h6 class="text-uppercase text-secondary mb-3"><i class="fas fa-location-dot me-2"></i>Datos actuales</h6>

        <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
            <div class="col">
                <div class="card text-center p-3 w-100 h-100 d-flex flex-column justify-content-between">
                    <div class="iconoClima"><i class="fas fa-temperature-half fa-2x text-danger"></i></div>
                    <div class="valorActual" id="temperatura">--</div>
                    <div class="texto">Temperatura (°C)</div>
                </div>
            </div>
            <div class="col">
                <div class="card text-center p-3 w-100 h-100 d-flex flex-column justify-content-between">
                    <div class="iconoClima"><i class="fas fa-droplet fa-2x text-primary"></i></div>
                    <div class="valorActual" id="humedad">--</div>
                    <div class="texto">Humedad (%)</div>
                </div>
            </div>
            <div class="col">
                <div class="card text-center p-3 w-100 h-100 d-flex flex-column justify-content-between">
                    <div class="iconoClima"><i class="fas fa-wind fa-2x text-info"></i></div>
                    <div class="valorActual" id="viento">--</div>
                    <div class="texto">Viento (km/h)</div>
                </div>
            </div>
            <div class="col">
                <div class="card text-center p-3 w-100 h-100 d-flex flex-column justify-content-between">
                    <div class="iconoClima"><i class="fas fa-compass fa-2x text-secondary"></i></div>
                    <div class="valorActual" id="direccion">--</div>
                    <div class="texto">Dirección viento (°)</div>
                </div>
            </div>
        </div>

        <h6 class="text-uppercase text-secondary mb-3"><i class="fas fa-chart-bar me-2"></i>Estadísticas históricas</h6>

        <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
            <div class="col">
                <div class="card p-3 w-100 h-100">
                    <div class="card-header mb-3"><i class="fas fa-temperature-half me-2 text-danger"></i>Temperatura
                        (°C)</div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="etiqueta-estadistica">Máxima</span>
                        <span class="valor-estadistica" id="temp-max">--</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="etiqueta-estadistica">Mínima</span>
                        <span class="valor-estadistica" id="temp-min">--</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="etiqueta-estadistica">Media</span>
                        <span class="valor-estadistica" id="temp-media">--</span>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card p-3 w-100 h-100">
                    <div class="card-header mb-3"><i class="fas fa-droplet me-2 text-primary"></i>Humedad (%)</div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="etiqueta-estadistica">Máxima</span>
                        <span class="valor-estadistica" id="hum-max">--</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="etiqueta-estadistica">Mínima</span>
                        <span class="valor-estadistica" id="hum-min">--</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="etiqueta-estadistica">Mediana</span>
                        <span class="valor-estadistica" id="hum-media">--</span>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card p-3 w-100 h-100">
                    <div class="card-header mb-3"><i class="fas fa-wind me-2 text-info"></i>Viento (km/h)</div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="etiqueta-estadistica">Máximo</span>
                        <span class="valor-estadistica" id="viento-max">--</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="etiqueta-estadistica">Mínimo</span>
                        <span class="valor-estadistica" id="viento-min">--</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="etiqueta-estadistica">Media</span>
                        <span class="valor-estadistica" id="viento-medio">--</span>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="text-uppercase text-secondary mb-3"><i class="fas fa-chart-line me-2"></i>Histórico de datos</h6>

        <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
            <div class="col">
                <div class="card p-3 w-100 h-100">
                    <div class="card-header mb-3"><i class="fas fa-temperature-half me-2 text-danger"></i>Temperatura
                    </div>
                    <canvas id="graficaTemperatura"></canvas>
                </div>
            </div>
            <div class="col">
                <div class="card p-3 w-100 h-100">
                    <div class="card-header mb-3"><i class="fas fa-droplet me-2 text-primary"></i>Humedad</div>
                    <canvas id="graficaHumedad"></canvas>
                </div>
            </div>
            <div class="col">
                <div class="card p-3 w-100 h-100">
                    <div class="card-header mb-3"><i class="fas fa-wind me-2 text-info"></i>Viento</div>
                    <canvas id="graficaViento"></canvas>
                </div>
            </div>

        </div>

<div class="d-flex justify-content-between align-items-center mb-3">
   	 <h6 class="text-uppercase text-secondary mb-0"><i class="fas fa-table me-2"></i>Evolución últimos 7 días</h6>

	<div class="row mb-3">

    	<div class="col-md-4">
        	<input type="date" id="fechaInicio" class="form-control" onchange="filtrarTabla()">
</div>
	 <div class="col-md-4">
        	<input type="date" id="fechaFin" class="form-control" onchange="filtrarTabla()">
    	</div>

    <div class="col-md-4 d-flex align-items-end">
        <button class="btn btn-outline-secondary btn-sm w-100" onclick="limpiarFiltros()">Limpiar filtro</button>
    </div>
</div>

    	<button class="btn btn-primary btn-sm mt-2" onclick="descargarCSV()"><i class="fas fa-download me-1"></i>Descargar datos</button>

	</div>
        <div class="card p-3 mb-4">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ciudad</th>
                            <th>Fecha y hora</th>
                            <th>Temperatura (°C)</th>
                            <th>Humedad (%)</th>
                            <th>Viento (km/h)</th>
                            <th>Dirección (°)</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-body"></tbody>
                </table>


            </div>
            <p class="text-muted small text-center mt-2">
                Se actualizan automáticamente cada 30 segundos · Total registros: <span id="total">--</span>
            </p>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/meteorologia.js') }}"></script>
</body>

</html>
