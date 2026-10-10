# Etapa 5: autenticación

La pantalla /login admite correo electrónico y contraseña. Dashboard, Equipos y Mantenimientos requieren una sesión autenticada. Cerrar sesión usa POST con protección CSRF, invalida la sesión y renueva el token.

El registro público está disponible en /register mediante Crear cuenta. Solicita nombre, correo único y contraseña confirmada de al menos 8 caracteres; guarda la contraseña con el hash de Laravel e inicia la sesión al terminar. No existe recuperación de contraseña ni roles o permisos. Administrador de demostración es el nombre del usuario de evaluación.

## Usuario de demostración

- Nombre: Administrador de demostración
- Correo: admin@example.com
- Contraseña: Admin123!

Con PHP 8.4.12 y MySQL de Laragon activo, desde la raíz del proyecto:

~~~powershell
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
npm run build
php artisan serve
~~~

También se puede ejecutar php artisan db:seed: DatabaseSeeder llama a AdminUserSeeder. El Seeder no duplica el usuario ni cambia su contraseña si ya existe ese correo.

Si php no está disponible en PATH:

~~~powershell
& 'C:\laragon\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe' artisan db:seed --class=AdminUserSeeder
& 'C:\laragon\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe' artisan serve
~~~

## Evaluación

1. Abrir http://127.0.0.1:8000/ en una ventana privada: debe redirigir a /login.
2. Enviar credenciales incorrectas: debe mostrar un error y conservar el correo, sin conservar la contraseña.
3. Iniciar sesión con las credenciales anteriores: debe abrir el Dashboard o la sección protegida que se intentó visitar.
4. Navegar por Equipos y Mantenimientos. Verificar que aparece el nombre del usuario y Cerrar sesión.
5. Cerrar sesión y volver a intentar abrir una ruta protegida: debe solicitar iniciar sesión nuevamente.
6. Revisar el login y la cabecera en una pantalla pequeña.

El login limita los envíos a cinco por minuto por dirección IP.

## Pruebas

~~~powershell
php artisan test
~~~

Las pruebas usan SQLite en memoria. Las pruebas de los módulos crean un usuario aislado y lo autentican; las pruebas de autenticación comprueban por separado el acceso anónimo.

Para comprobar el registro, usar Crear cuenta desde el Login, registrar un correo nuevo y verificar la redirección al Dashboard. Repetir con un correo existente o contraseñas distintas debe mostrar errores sin crear otra cuenta.
