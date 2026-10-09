<nav aria-label="Navegación principal" class="space-y-2 p-4">
    @foreach (['dashboard' => 'Dashboard', 'equipos.index' => 'Equipos', 'mantenimientos.index' => 'Mantenimientos'] as $route => $label)
        @php($active = request()->routeIs(str_ends_with($route, '.index') ? str_replace('.index', '.*', $route) : $route))
        <a href="{{ route($route) }}"
           @if ($active) aria-current="page" @endif
           @class(['block rounded-lg px-4 py-3 text-sm font-medium transition focus-visible:outline-2 focus-visible:outline-teal-300', 'bg-teal-700 text-white' => $active, 'text-slate-300 hover:bg-slate-800 hover:text-white' => ! $active])>
            {{ $label }}
        </a>
    @endforeach
</nav>
