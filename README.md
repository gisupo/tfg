# Sistema Meteorológico - Gandia

Proyecto Final de Grado Superior de Desarrollo de Aplicaciones Web (DAW)
I.E.S. Maria Enríquez - Gandia - Curso 2025/2026

## Descripción

Aplicación web orientada a la obtención de datos desde fuentes externas, su procesamiento y almacenamiento en una base de datos. El sistema obtiene datos meteorológicos en tiempo real de varias ciudades españolas mediante la API Open-Meteo, los procesa y almacena en una base de datos MySQL, permitiendo gestionar y consultar la información recopilada mediante una interfaz web con gráficas interactivas.

## Tecnologías utilizadas

- **Backend:** PHP 8 + Laravel 11
- **Base de datos:** MySQL 8 (Docker)
- **Frontend:** HTML, CSS, Bootstrap 5, Chart.js, Font Awesome
- **Entorno:** Laravel Sail + Docker
- **API externa:** Open-Meteo

## Funcionalidades

- ETL automática cada hora mediante el scheduler de Laravel
- API REST con endpoints para consultar datos y estadísticas
- Visualización de datos con gráficas de temperatura, humedad y viento
- Tabla con historial de registros
- Estadísticas históricas (máximos, mínimos y medias)
- Botón para actualizar datos manualmente

## Instalación

1. Clonar el repositorio
2. Copiar `.env.example` a `.env` y configurar las variables
3. Ejecutar `./vendor/bin/sail up -d`
4. Ejecutar `./vendor/bin/sail artisan migrate --seed`
5. Ejecutar `./vendor/bin/sail artisan etl:ejecutar`
6. Abrir `http://localhost/meteorologia`

## Autor

Giselle Suazo Posas - DAW 2025/2026
