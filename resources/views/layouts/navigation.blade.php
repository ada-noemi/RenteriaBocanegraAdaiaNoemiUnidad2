<nav aria-label="Navegación principal" class="space-y-2 p-4">
    @foreach (['dashboard' => 'Dashboard', 'equipos.index' => 'Equipos', 'mantenimientos.index' => 'Mantenimientos'] as $route => $label)
        <a href="{{ route($route) }}"
           @if (request()->routeIs($route)) aria-current="page" @endif
           @class(['block rounded-lg px-4 py-3 text-sm font-medium transition focus-visible:outline-2 focus-visible:outline-teal-300', 'bg-teal-700 text-white' => request()->routeIs($route), 'text-slate-300 hover:bg-slate-800 hover:text-white' => ! request()->routeIs($route)])>
            {{ $label }}
        </a>
    @endforeach
</nav>
