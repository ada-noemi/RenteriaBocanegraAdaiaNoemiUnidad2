@php($registerErrors = new \Illuminate\Support\MessageBag(session('register_errors', [])))
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear cuenta | Gestión de Mantenimiento</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-md overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="bg-slate-900 p-6 text-white">
                <p class="text-xs font-semibold uppercase tracking-widest text-teal-300">Panel administrativo</p>
                <h1 class="mt-2 text-2xl font-semibold">Crear cuenta</h1>
                <p class="mt-2 text-sm text-slate-300">Sistema Web de Gestión de Mantenimiento</p>
            </div>
            <form action="{{ route('register.store') }}" method="POST" class="space-y-5 p-6">
                @csrf
                @if ($registerErrors->any())
                    <div role="alert" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">Revisa los campos indicados.</div>
                @endif
                @foreach (['name' => ['Nombre', 'text', 'name'], 'email' => ['Correo electrónico', 'email', 'username'], 'password' => ['Contraseña', 'password', 'new-password'], 'password_confirmation' => ['Confirmar contraseña', 'password', 'new-password']] as $field => [$label, $type, $autocomplete])
                    <div>
                        <label for="{{ $field }}" class="mb-2 block text-sm font-medium">{{ $label }}</label>
                        <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" required maxlength="255" autocomplete="{{ $autocomplete }}"
                               @if ($type !== 'password') value="{{ old($field) }}" @else minlength="8" @endif
                               @if ($field === 'name') autofocus @endif
                               @if ($registerErrors->has($field)) aria-invalid="true" aria-describedby="{{ $field }}-error" @endif
                               class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200">
                        @if ($registerErrors->has($field))
                            <p id="{{ $field }}-error" class="mt-2 text-sm text-red-700">{{ $registerErrors->first($field) }}</p>
                        @endif
                    </div>
                @endforeach
                <p class="text-xs text-slate-500">La contraseña debe tener al menos 8 caracteres.</p>
                <button type="submit" class="w-full rounded-lg bg-teal-700 px-5 py-3 text-sm font-medium text-white hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700">Crear cuenta</button>
                <p class="text-center text-sm text-slate-600">¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="font-medium text-teal-700 hover:underline">Iniciar sesión</a></p>
            </form>
        </section>
    </main>
</body>
</html>
