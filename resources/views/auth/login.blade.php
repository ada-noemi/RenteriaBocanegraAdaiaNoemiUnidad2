@php($loginErrors = new \Illuminate\Support\MessageBag(session('login_errors', [])))
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión | Gestión de Mantenimiento</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-md overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="bg-slate-900 p-6 text-white">
                <p class="text-xs font-semibold uppercase tracking-widest text-teal-300">Panel administrativo</p>
                <h1 class="mt-2 text-2xl font-semibold">Gestión de Mantenimiento</h1>
                <p class="mt-2 text-sm text-slate-300">Inicia sesión para acceder al sistema.</p>
            </div>
            <form action="{{ route('login.store') }}" method="POST" class="space-y-6 p-6">
                @csrf
                @if ($loginErrors->any())
                    <div role="alert" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                        {{ $loginErrors->first() }}
                    </div>
                @endif
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium">Correo electrónico</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" autocomplete="username" autofocus
                           @if ($loginErrors->has('email')) aria-invalid="true" @endif
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200">
                </div>
                <div>
                    <label for="password" class="mb-2 block text-sm font-medium">Contraseña</label>
                    <input id="password" name="password" type="password" required maxlength="255" autocomplete="current-password"
                           @if ($loginErrors->has('password')) aria-invalid="true" @endif
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200">
                </div>
                <button type="submit" class="w-full rounded-lg bg-teal-700 px-5 py-3 text-sm font-medium text-white hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700">Iniciar sesión</button>
                <p class="text-center text-sm text-slate-600">¿No tienes una cuenta? <a href="{{ route('register') }}" class="font-medium text-teal-700 hover:underline">Crear cuenta</a></p>
            </form>
        </section>
    </main>
</body>
</html>
