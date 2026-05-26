//1. Cargamos la API
const API_URL = 'http://localhost/api/meteorologia';

//Configuramos para que las tres gráficas se vean ordenadas
const opcionesGraficas = {
    responsive: true,
    plugins: { legend: { display: false } }
};

//2. Inicializamos las tres gráficas vacías usando Chart.js
const graficaTemp = new Chart(document.getElementById('graficaTemperatura'), {
    type: 'line',
    data: { labels: [], datasets: [{ data: [], borderColor: '#e74c3c', backgroundColor: 'rgba(231,76,60,0.1)', fill: true }] },
    options: opcionesGraficas
});

const graficaHum = new Chart(document.getElementById('graficaHumedad'), {
    type: 'line',
    data: { labels: [], datasets: [{ data: [], borderColor: '#2d7dd2', backgroundColor: 'rgba(45,125,210,0.1)', fill: true }] },
    options: opcionesGraficas
});

const graficaViento = new Chart(document.getElementById('graficaViento'), {
    type: 'bar',
    data: { labels: [], datasets: [{ data: [], backgroundColor: 'rgba(26,58,92,0.7)', borderColor: '#1a3a5c', borderWidth: 1 }] },
    options: opcionesGraficas
});

//3. Cargarmos los datos usando promesas .then()
function cargarDatosDelClima() {
    
    // PETICIÓN 1: Obtenemos el historial completo
    fetch(API_URL + '/datos')
        .then(respuesta => respuesta.json())
        .then(datos => {
            
            if (!datos || datos.length === 0){
                return;
            }
            //Ordenamos los datos del más nuevo al más antiguo (índice 0)
            const ultimoRegistro = datos[0] || {};
            
            //Rellenamos las tarjetas con los datos actuales
            document.getElementById('temperatura').textContent = ultimoRegistro.temperatura + ' °C';
            document.getElementById('humedad').textContent = ultimoRegistro.humedad + ' %';
            document.getElementById('viento').textContent = ultimoRegistro.velocidad_viento + ' km/h';
            document.getElementById('direccion').textContent = ultimoRegistro.direccion_viento + '°';

            //Extraemos las fechas para los ejes X de las gráficas
            const etiquetasFechas = datos.map(registro => 
                new Date(registro.fecha_hora).toLocaleString()
);

            //Actualizamos los datos de la gráfica de Temperatura
            graficaTemp.data.labels = etiquetasFechas;
            graficaTemp.data.datasets[0].data = datos.map(registro => registro.temperatura);
            graficaTemp.update();

            //Actualizamos los datos de la gráfica de Humedad
            graficaHum.data.labels = etiquetasFechas;
            graficaHum.data.datasets[0].data = datos.map(registro => registro.humedad);
            graficaHum.update();

            //Actualizamos los datos de la gráfica de Viento
            graficaViento.data.labels = etiquetasFechas;
            graficaViento.data.datasets[0].data = datos.map(registro => registro.velocidad_viento);
            graficaViento.update();

            //Limpiamos y rellenamos la tabla HTML
            const tablaBody = document.getElementById('tabla-body');
            tablaBody.innerHTML = '';
            
            datos.forEach(registro => {
                tablaBody.innerHTML += `
                    <tr>
                        <td>${registro.id}</td>
                        <td>${new Date(registro.fecha_hora).toLocaleString()}</td>
                        <td>${registro.temperatura} °C</td>
                        <td>${registro.humedad} %</td>
                        <td>${registro.velocidad_viento} km/h</td>
                        <td>${registro.direccion_viento}°</td>
                    </tr>`;
            });
        })
        .catch(error => console.error('Error al cargar el historial:', error));

    //PETICIÓN 2: Obtener las estadísticas calculadas en Laravel
    fetch(API_URL + '/estadisticas')
        .then(respuesta => respuesta.json())
        .then(estadisticas => {
            //Rellenamos las tarjetas de estadísticas usando sus IDs
            document.getElementById('temp-max').textContent = estadisticas.temp_max + ' °C';
            document.getElementById('temp-min').textContent = estadisticas.temp_min + ' °C';
            document.getElementById('temp-media').textContent = estadisticas.temp_media + ' °C';

            document.getElementById('hum-max').textContent = estadisticas.humedad_max + ' %';
            document.getElementById('hum-min').textContent = estadisticas.humedad_min + ' %';
            document.getElementById('hum-media').textContent = estadisticas.humedad_media + ' %';

            document.getElementById('viento-max').textContent = estadisticas.viento_max + ' km/h';
            document.getElementById('viento-min').textContent = estadisticas.viento_min + ' km/h';
            document.getElementById('viento-medio').textContent = estadisticas.viento_medio + ' km/h';

            document.getElementById('total').textContent = estadisticas.total_registros;
        })
        .catch(error => console.error('Error al cargar estadísticas:', error));
}

//Función para ejercutar la ETL desde el botón
        function ejecutarETL() {
            fetch(API_URL + '/etl', { method: 'POST' })
            .then(respuesta => respuesta.json())
            .then(resultado => {
                console.log(resultado);
                cargarDatosDelClima();
                //Mensaje de éxito
                const mensaje = document.getElementById('mensaje');
                mensaje.classList.remove('d-none');
                setTimeout(() => console.error('Error al ejecutar la ETL:', error));
            })
        }

//4. Ejecutamos la función automáticamente al cargar la página por primera vez
document.addEventListener("DOMContentLoaded", cargarDatosDelClima);

//Refresco automática
setInterval(() =>{
    
    cargarDatosDelClima();

    document.getElementById('hora-actual').textContent = new Date().toLocaleString();

    console.log("refrescando datos...");
},30000);
