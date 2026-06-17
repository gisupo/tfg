//URL base de la API de Laravel
const API_URL = "https://tfg-production-d219.up.railway.app/api/meteorologia";

//Guardamos el ID de la ciudad seleccionada como predeterminada en este caso elijo Gandía
let ciudadSeleccionada = 1;

//Configuración común para las tres gráficas
const opcionesGraficas = {
    responsive: true,
    plugins: {
        legend: { display: false }
    }
};

//Creamos las tres gráficas vacías al cargar la página
const graficaTemperatura = new Chart(document.getElementById("graficaTemperatura"), {
    type: "line",
    data: {
        labels: [],
        datasets: [{ data: [], borderColor: "#e74c3c", backgroundColor: "rgba(231,76,60,0.1)", fill: true }]
    },
    options: opcionesGraficas
});

const graficaHumedad = new Chart(document.getElementById("graficaHumedad"), {
    type: "line",
    data: {
        labels: [],
        datasets: [{ data: [], borderColor: "#3b82f6", backgroundColor: "rgba(59,130,246,0.15)", fill: true }]
    },
    options: opcionesGraficas
});

const graficaViento = new Chart(document.getElementById("graficaViento"), {
    type: "bar",
    data: {
        labels: [],
        datasets: [{ data: [], backgroundColor: "rgba(26,58,92,0.7)", borderColor: "#1a3a5c", borderWidth: 1 }]
    },
    options: opcionesGraficas
});

//Se ejecuta cuando el usuario cambia la ciudad en el selector
function cambiarCiudad() {
    const selector = document.getElementById("selectorCiudad");
    ciudadSeleccionada = selector.value || null;

    //Actualizamos el título con la ciudad seleccionada
    const titulo = document.getElementById("tituloCiudad");
    if (ciudadSeleccionada) {
        titulo.textContent = selector.options[selector.selectedIndex].text;
    } else {
        titulo.textContent = "Todas las ciudades";
    }

    //Recargamos todos los datos con la nueva ciudad
    cargarDatosDelClima();
}

//Función principal: carga tarjetas, gráficas y tabla
function cargarDatosDelClima() {

    //Si hay ciudad elegida usamos el endpoint filtrado, si no el general
    const urlDatos = ciudadSeleccionada
        ? API_URL + "/ciudad/" + ciudadSeleccionada
        : API_URL + "/datos";

    //PETICIÓN 1: historial de registros
    fetch(urlDatos)
        .then((respuesta) => respuesta.json())
        .then((datos) => {
            if (!datos || datos.length === 0) {
                return;
            }

            //El primer registro es el más reciente (ordenados de nuevo a antiguo)
            const ultimoRegistro = datos[0];

            //Rellenamos las tarjetas de datos actuales
            document.getElementById("temperatura").textContent = ultimoRegistro.temperatura + " °C";
            document.getElementById("humedad").textContent = ultimoRegistro.humedad + " %";
            document.getElementById("viento").textContent = ultimoRegistro.velocidad_viento + " km/h";
            document.getElementById("direccion").textContent = ultimoRegistro.direccion_viento + "°";

            //Fechas para el eje X de las gráficas
            const etiquetasFechas = datos.map((registro) =>
                new Date(registro.fecha_hora).toLocaleString()
            );

            //Actualizamos la gráfica de temperatura
            graficaTemperatura.data.labels = etiquetasFechas;
            graficaTemperatura.data.datasets[0].data = datos.map((r) => r.temperatura);
            graficaTemperatura.update();

            //Actualizamos la gráfica de humedad
            graficaHumedad.data.labels = etiquetasFechas;
            graficaHumedad.data.datasets[0].data = datos.map((r) => r.humedad);
            graficaHumedad.update();

            //Actualizamos la gráfica de viento
            graficaViento.data.labels = etiquetasFechas;
            graficaViento.data.datasets[0].data = datos.map((r) => r.velocidad_viento);
            graficaViento.update();

            //Limpiamos y rellenamos la tabla
            const tablaBody = document.getElementById("tabla-body");
            tablaBody.innerHTML = "";
            datos.forEach((registro) => {
                tablaBody.innerHTML += `
                    <tr>
                        <td>${registro.id}</td>
                        <td>${registro.ciudad}</td>
                        <td>${new Date(registro.fecha_hora).toLocaleString()}</td>
                        <td>${registro.temperatura} °C</td>
                        <td>${registro.humedad} %</td>
                        <td>${registro.velocidad_viento} km/h</td>
                        <td>${registro.direccion_viento}°</td>
                    </tr>`;
            });

	filtrarTabla();

        })
        .catch((error) => console.error("Error al cargar el historial:", error));

    //PETICIÓN 2: estadísticas (siempre globales)
    fetch(API_URL + "/estadisticas")
        .then((respuesta) => respuesta.json())
        .then((estadisticas) => {
            document.getElementById("temp-max").textContent = estadisticas.temp_max + " °C";
            document.getElementById("temp-min").textContent = estadisticas.temp_min + " °C";
            document.getElementById("temp-media").textContent = estadisticas.temp_media + " °C";
            document.getElementById("hum-max").textContent = estadisticas.humedad_max + " %";
            document.getElementById("hum-min").textContent = estadisticas.humedad_min + " %";
            document.getElementById("hum-media").textContent = estadisticas.humedad_media + " %";
            document.getElementById("viento-max").textContent = estadisticas.viento_max + " km/h";
            document.getElementById("viento-min").textContent = estadisticas.viento_min + " km/h";
            document.getElementById("viento-medio").textContent = estadisticas.viento_medio + " km/h";
            document.getElementById("total").textContent = estadisticas.total_registros;
        })
        .catch((error) => console.error("Error al cargar estadísticas:", error));
}

//Ejecuta la ETL desde el botón del navbar
function ejecutarETL() {
    fetch(API_URL + "/etl", { method: "POST" })
        .then((respuesta) => respuesta.json())
        .then((resultado) => {
            console.log(resultado);
            cargarDatosDelClima();
            const mensaje = document.getElementById("mensaje");
            mensaje.classList.remove("d-none");
            setTimeout(() => mensaje.classList.add("d-none"), 3000);
        })
        .catch((error) => console.error("Error al ejecutar la ETL:", error));
}

//Al cargar la página: cargamos ciudades y datos
document.addEventListener("DOMContentLoaded", function () {
    cargarDatosDelClima();
    document.getElementById("tituloCiudad").textContent = "Gandía (Valencia";
});

//Refresco automático cada 30 segundos
setInterval(() => {
    cargarDatosDelClima();
    console.log("Refrescando datos...");
}, 30000);


//Función para descargar los datos de la tabla en formato CSV 

function descargarCSV() {
    const filas = [['ID', 'Ciudad', 'Fecha y hora', 'Temperatura (°C)', 'Humedad (%)', 'Viento (km/h)', 'Dirección (°)']];
    const tbody = document.getElementById('tabla-body');
    const filasTbody = tbody.querySelectorAll('tr');
    filasTbody.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        filas.push(Array.from(celdas).map(td => td.textContent));
    });
    const csv = filas.map(f => f.join(';')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'datos_meteorologicos.csv';
    a.click();
}


function filtrarTabla() {
    const inicio = document.getElementById('fechaInicio').value;
    const fin = document.getElementById('fechaFin').value;
    const filas = document.querySelectorAll('#tabla-body tr');

    filas.forEach(fila => {
        const texto = fila.querySelectorAll('td')[2].textContent.trim();
        const partes = texto.split(',')[0].split('/');
        const dia = parseInt(partes[0]);
        const mes = parseInt(partes[1]) - 1;
        const anyo = parseInt(partes[2]);
        const fechaFila = `${anyo}-${String(mes+1).padStart(2,'0')}-${String(dia).padStart(2,'0')}`;
        let mostrar = true;
        if (inicio) mostrar = mostrar && fechaFila >= inicio;
        if (fin) mostrar = mostrar && fechaFila <= fin;
        fila.style.display = mostrar ? '' : 'none';
    });
}
function limpiarFiltros() {
    document.getElementById('fechaInicio').value = '';
    document.getElementById('fechaFin').value = '';
    document.querySelectorAll('#tabla-body tr').forEach(f => f.style.display = '');
}
