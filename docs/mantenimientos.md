# Etapa 3: Mantenimientos

Usar la rama feature/maintenance-module, PHP 8.4.12 y MySQL de Laragon activo.

## Preparación

La base gestion_mantenimiento y el módulo de Equipos deben estar configurados.

~~~powershell
php artisan migrate
npm run build
php artisan serve
~~~

La nueva migración 2026_10_08_000004_create_mantenimientos_table crea mantenimientos y una clave foránea hacia equipos. No se agregan datos de ejemplo.

## Prueba manual

1. Abrir http://127.0.0.1:8000/equipos y registrar un equipo si el listado está vacío.
2. Abrir http://127.0.0.1:8000/mantenimientos y pulsar Registrar mantenimiento.
3. En el modal seleccionar un equipo, tipo Preventivo o Correctivo, fecha programada, descripción y estado. Guardar y comprobar el mensaje de éxito y el registro en el listado.
4. Pulsar Editar. Cambiar descripción, equipo o tipo y pasar el estado a En proceso o Finalizado. Comprobar los cambios.
5. Dejar un campo obligatorio vacío: el navegador debe impedir enviar. Si el servidor rechaza los datos, reabre el modal con los errores y conserva lo ingresado.
6. Pulsar Eliminar y cancelar: el mantenimiento debe permanecer. Confirmar después: debe desaparecer y el equipo debe seguir existiendo.
7. Intentar eliminar un equipo con mantenimientos: se muestra un aviso y se conservan ambos. Para eliminarlo, eliminar primero sus mantenimientos.
8. Revisar menú y modales en pantalla pequeña. Las tablas permiten desplazamiento horizontal.
9. Verificar que Dashboard mantiene sus tarjetas en cero y su tabla inicial.

Todos los formularios y confirmaciones usan modales. La descripción es obligatoria y admite hasta 5000 caracteres. Equipo existente, tipo, fecha válida y estado son obligatorios. El módulo admite equipos activos o fuera de servicio.

## Pruebas

~~~powershell
php artisan test
php artisan route:list --except-vendor
php artisan migrate:status
~~~

Las pruebas Feature usan SQLite en memoria; no modifican los registros de MySQL. También se verificó el CRUD, la relación y la restricción de borrado directamente en MySQL dentro de una transacción revertida.
