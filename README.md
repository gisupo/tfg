# Sistema Integral para la ingesta, transformación y representación de datos meteorológicos

Proyecto Final de Grado Superior de Desarrollo de Aplicaciones Web (DAW)
I.E.S. Maria Enríquez - Gandia - Curso 2025/2026

## Descripción

Aplicación web que obtiene datos meteorológicos en tiempo real de ciudades españolas mediante la API Open-Meteo, los procesa y almacena en una base de datos MySQL, y los visualiza mediante una interfaz web con gráficas interactivas.

## 🌐 Demo en producción

https://tfg-production-d219.up.railway.app/meteorologia

## Tecnologías utilizadas

- **Backend:** PHP 8.2 + Laravel 12
- **Base de datos:** MySQL 9
- **Frontend:** HTML, CSS, Bootstrap 5, Chart.js, Font Awesome
- **Entorno local:** Laravel Sail + Docker
- **Despliegue:** Railway (PaaS)
- **API externa:** Open-Meteo

## Funcionalidades

- ETL automática cada hora mediante el scheduler de Laravel
- API REST con endpoints para consultar datos y estadísticas
- Visualización de datos con gráficas de los últimos 7 días
- Filtro por rango de fechas
- Paginación de registros
- Descarga de datos en CSV
- Estadísticas históricas (máximos, mínimos y medias)
- Botón para actualizar datos manualmente
- Principales ciudades de España

## Instalación local

1. Clonar el repositorio
2. Copiar `.env.example` a `.env` y configurar las variables
3. Ejecutar `./vendor/bin/sail up -d`
4. Ejecutar `./vendor/bin/sail artisan migrate`
5. Ejecutar `./vendor/bin/sail artisan db:seed`
6. Ejecutar `./vendor/bin/sail artisan etl:ejecutar`
7. Abrir `http://localhost/meteorologia`

## Autor

Giselle Suazo Posas - DAW 2025/2026
