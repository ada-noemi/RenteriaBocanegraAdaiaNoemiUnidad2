# Etapa 2: MySQL y Equipos

## Preparación local

Usar PHP 8.4.12 e iniciar MySQL desde Laragon. La configuración local usa:

- Host: 127.0.0.1
- Puerto: 3306
- Base de datos: gestion_mantenimiento
- Usuario: root
- Contraseña: vacía en la instalación local comprobada. Si cambia, actualizar DB_PASSWORD en .env.

Para una instalación nueva, crear la base en la consola SQL de phpMyAdmin:

~~~sql
CREATE DATABASE IF NOT EXISTS gestion_mantenimiento
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
~~~

Configurar .env a partir de .env.example. No subir .env ni credenciales a Git.

~~~powershell
php artisan key:generate
php artisan config:clear
php artisan migrate
npm install
npm run build
php artisan serve
~~~

Las migraciones iniciales crean users, password_reset_tokens, sessions, cache, cache_locks, jobs, job_batches y failed_jobs. La migración de esta etapa crea equipos. Las sesiones locales siguen usando archivos.

Si php no está disponible en el PATH, sustituirlo por:

~~~powershell
& 'C:\laragon\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe' artisan serve
~~~

## Prueba manual del módulo

1. Abrir http://127.0.0.1:8000/equipos. El listado muestra un estado vacío al comenzar.
2. Seleccionar Registrar equipo. Se abre un modal dentro del listado. Completar nombre, código único, tipo, ubicación, estado y fecha.
3. Guardar y comprobar el mensaje de éxito y el equipo en el listado.
4. Intentar registrar otro equipo con el mismo código. Debe mostrar un error y conservar los datos.
5. Abrir el modal Editar del equipo; cambiar ubicación y estado a Fuera de servicio. Conservar el código original debe estar permitido.
6. Si hay un segundo equipo, intentar asignarle el código del primero: debe rechazarse.
7. Seleccionar Eliminar y cancelar el modal de confirmación: el registro debe permanecer. Confirmar después: debe desaparecer.
8. Comprobar las tres secciones y el menú en una pantalla pequeña. Dashboard y Mantenimientos permanecen en su etapa inicial.

Todos los campos son obligatorios. Límites: nombre y ubicación 255 caracteres, código 50 y tipo 100. Estados permitidos: Activo y Fuera de servicio. La fecha debe ser válida.

## Comprobaciones automáticas

~~~powershell
php artisan test
php artisan route:list --except-vendor
php artisan migrate:status
~~~

Las pruebas usan SQLite en memoria, no la base MySQL local. No se agregan equipos de ejemplo ni seeders.

Todos los formularios del módulo se muestran en modales. Los errores de validación reabren el modal correspondiente y conservan los datos. Se pueden cerrar con Cancelar, Escape o pulsando fuera del modal.
