let ciudadActual = "Gandía";
//1. Cargamos la API
const API_URL = "http://localhost/api/meteorologia";

//Configuramos las gráficas
const opcionesGraficas = {
    responsive: true,
    plugins: {
        legend: {
            display: false
        }
    }
};

//2. Inicializamos las tres gráficas con Chart.js
const graficaTemperatura = new Chart(
    document.getElementById("graficaTemperatura"), {
        type: "line",
        data: {
            labels: [],
            datasets: [{
                data: [],
                borderColor: "#e74c3c",
                backgroundColor: "rgba(231,76,60,0.1)",
                fill: true,
            }]
        },
        options: opcionesGraficas
    }
);

const graficaHumedad = new Chart(document.getElementById("graficaHumedad"), {
    type: "line",
    data: {
        labels: [],
        datasets: [{
            data: [],
            borderColor: "#3b82f6",
            backgroundColor: "rgba(59,130,246,0.15)",
            fill: true,
        }]
    },
    options: opcionesGraficas,
});

const graficaViento = new Chart(document.getElementById("graficaViento"), {
    type: "bar",
    data: {
        labels: [],
        datasets: [{
            data: [],
            backgroundColor: "rgba(26,58,92,0.7)",
            borderColor: "#1a3a5c",
            borderWidth: 1,
        }]
    },
    options: opcionesGraficas,
});


//3. Cargarmos los datos
function cargarDatosDelClima() {

    // PETICIÓN 1: Obtenemos el historial completo
    fetch(API_URL + "/datos")
        .then((respuesta) => respuesta.json())
        .then((datos) => {

            if (!datos || datos.length === 0) {
                return; // ✔ FIX: return correcto
            }

            //Ordenamos los datos del más nuevo al más antiguo
            const ultimoRegistro = datos[0] || {};

            //Rellenamos las tarjetas con los datos actuales
            document.getElementById("temperatura").textContent =
                ultimoRegistro.temperatura + " °C";
            document.getElementById("humedad").textContent =
                ultimoRegistro.humedad + " %";
            document.getElementById("viento").textContent =
                ultimoRegistro.velocidad_viento + " km/h";
            document.getElementById("direccion").textContent =
                ultimoRegistro.direccion_viento + "°";

            //Extraemos las fechas para los ejes X de las gráficas
            const etiquetasFechas = datos.map((registro) =>
                new Date(registro.fecha_hora).toLocaleString()
            );

            //Actualizamos gráfica Temperatura
            graficaTemperatura.data.labels = etiquetasFechas;
            graficaTemperatura.data.datasets[0].data =
                datos.map((r) => r.temperatura);
            graficaTemperatura.update();

            //Actualizamos gráfica Humedad
            graficaHumedad.data.labels = etiquetasFechas;
            graficaHumedad.data.datasets[0].data =
                datos.map((r) => r.humedad);
            graficaHumedad.update();

            //Actualizamos gráfica Viento
            graficaViento.data.labels = etiquetasFechas;
            graficaViento.data.datasets[0].data =
                datos.map((r) => r.velocidad_viento);
            graficaViento.update();

            //Limpiamos tabla HTML
            const tablaBody = document.getElementById("tabla-body");
            tablaBody.innerHTML = "";

            datos.forEach((registro) => {
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
        .catch((error) =>
            console.error("Error al cargar el historial:", error)
        );


    //PETICIÓN 2: Obtener las estadísticas calculadas en Laravel
    fetch(API_URL + "/estadisticas")
        .then((respuesta) => respuesta.json())
        .then((estadisticas) => {

            document.getElementById("temp-max").textContent =
                estadisticas.temp_max + " °C";
            document.getElementById("temp-min").textContent =
                estadisticas.temp_min + " °C";
            document.getElementById("temp-media").textContent =
                estadisticas.temp_media + " °C";

            document.getElementById("hum-max").textContent =
                estadisticas.humedad_max + " %";
            document.getElementById("hum-min").textContent =
                estadisticas.humedad_min + " %";
            document.getElementById("hum-media").textContent =
                estadisticas.humedad_media + " %";

            document.getElementById("viento-max").textContent =
                estadisticas.viento_max + " km/h";
            document.getElementById("viento-min").textContent =
                estadisticas.viento_min + " km/h";
            document.getElementById("viento-medio").textContent =
                estadisticas.viento_medio + " km/h";

            document.getElementById("total").textContent =
                estadisticas.total_registros;
        })
        .catch((error) =>
            console.error("Error al cargar estadísticas:", error)
        );
}


//Añadimos ciudades
function buscarCiudad() {
    const ciudad = document.getElementById("ciudad").value;

    if (!ciudad) {
        alert("Escribe una ciudad");
        return;
    }

    fetch(`https://api.openweathermap.org/data/2.5/weather?q=${ciudad}&appid=c21a75abac5823d17be04db82fa17853&units=metric&lang=es`)
        .then(r => r.json())
        .then(datos => {

            if (datos.cod !== 200) {
                document.getElementById("resultadoCiudad").innerHTML =
                    `<p class="text-danger">Ciudad no encontrada</p>`;
                return;
            }

            document.getElementById("resultadoCiudad").innerHTML = `
                <div class="card p-3 mt-3">
                    <h5>${datos.name}</h5>
                    <p>Temperatura: ${datos.main.temp} °C</p>
                    <p>Humedad: ${datos.main.humidity} %</p>
                    <p>Viento: ${datos.wind.speed} km/h</p>
                    <p>Estado: ${datos.weather[0].description}</p>
                </div>
            `;
        })
        .catch(err => console.error(err));
}

function cambiarCiudad() {
    const input = document.getElementById("ciudad");

    ciudadActual = input.value;

    if (!ciudadActual) {
        alert("Escribe una ciudad");
        return;
    }

    cargarCiudad();
}

function cargarCiudad() {

    fetch(`https://api.openweathermap.org/data/2.5/weather?q=${ciudadActual}&appid=c21a75abac5823d17be04db82fa17853&units=metric&lang=es`)
        .then(r => r.json())
        .then(datos => {

            if (datos.cod !== 200) {
                document.getElementById("resultadoCiudad").innerHTML =
                    `<p class="text-danger mt-2">Ciudad no encontrada</p>`;
                return;
            }

            // 🔥 ACTUALIZAR SOLO LAS TARJETAS PRINCIPALES
            document.getElementById("temperatura").textContent = datos.main.temp + " °C";
            document.getElementById("humedad").textContent = datos.main.humidity + " %";
            document.getElementById("viento").textContent = datos.wind.speed + " km/h";
            document.getElementById("direccion").textContent = datos.wind.deg + "°";

            // opcional: mostrar info abajo
            document.getElementById("resultadoCiudad").innerHTML = `
                <div class="alert alert-info mt-3">
                    Mostrando datos de: <b>${datos.name}</b>
                </div>
            `;
        })
        .catch(err => {
            console.error(err);
        });
}

//Función para ejecutar la ETL desde el botón
function ejecutarETL() {
    fetch(API_URL + "/etl", { method: "POST" })
        .then((respuesta) => respuesta.json())
        .then((resultado) => {
            console.log(resultado);

            cargarDatosDelClima();

            const mensaje = document.getElementById("mensaje");
            mensaje.classList.remove("d-none");

            setTimeout(() => mensaje.classList.add("d-none"), 3000);
        });
}


//4. Ejecutamos la función automáticamente al cargar la página por primera vez
document.addEventListener("DOMContentLoaded", cargarDatosDelClima, cargarCiudad);


//Refresco automática
setInterval(() => {
    cargarDatosDelClima();
    console.log("refrescando datos...");
}, 30000);