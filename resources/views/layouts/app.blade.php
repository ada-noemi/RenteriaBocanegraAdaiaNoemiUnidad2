<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Gestión de Mantenimiento</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased">
    <div class="min-h-screen md:flex">
        <aside class="bg-slate-900 text-slate-100 md:fixed md:inset-y-0 md:w-64">
            <div class="border-b border-slate-700 px-6 py-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-teal-300">Panel administrativo</p>
                <p class="mt-2 text-xl font-semibold">Gestión de<br>Mantenimiento</p>
            </div>
            <details class="group md:hidden">
                <summary class="cursor-pointer px-6 py-4 font-medium focus-visible:outline-2 focus-visible:outline-teal-300">Menú de navegación</summary>
                @include('layouts.navigation')
            </details>
            <div class="hidden md:block">
                @include('layouts.navigation')
            </div>
        </aside>

        <div class="min-w-0 flex-1 md:ml-64">
            <header class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 bg-white px-6 py-5 lg:px-10">
                <div>
                <p class="text-sm text-slate-500">Sistema Web de Gestión de Mantenimiento</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight">@yield('title')</h1>
                </div>
                @auth
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <span class="max-w-64 break-words font-medium">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 font-medium hover:bg-slate-50">Cerrar sesión</button>
                        </form>
                    </div>
                @endauth
            </header>
            <main id="contenido" class="mx-auto max-w-7xl space-y-6 p-6 lg:p-10">
                @yield('content')
            </main>
            <footer class="px-6 pb-6 text-xs text-slate-500 lg:px-10">Proyecto académico · Gestión de equipos y mantenimientos</footer>
        </div>
    </div>
</body>
</html>
