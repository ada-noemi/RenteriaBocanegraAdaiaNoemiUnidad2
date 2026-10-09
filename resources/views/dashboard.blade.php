@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <p class="text-sm text-slate-600">Resumen general de equipos y actividades de mantenimiento.</p>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach (['Total de equipos', 'Equipos activos', 'Mantenimientos pendientes', 'Mantenimientos finalizados'] as $label)
            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-medium text-slate-600">{{ $label }}</h2>
                <p class="mt-4 text-4xl font-semibold text-slate-900">0</p>
            </section>
        @endforeach
    </div>
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 p-6">
            <h2 class="text-lg font-semibold">Mantenimientos recientes</h2>
            <a href="{{ route('mantenimientos.index') }}" class="text-sm font-medium text-teal-700 hover:underline">Ver mantenimientos →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px] text-left text-sm">
                <caption class="sr-only">Listado de mantenimientos recientes</caption>
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        @foreach (['Equipo', 'Tipo', 'Fecha programada', 'Estado'] as $column)
                            <th scope="col" class="px-6 py-4 font-medium">{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody><tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">Todavía no existen registros de mantenimientos.</td></tr></tbody>
            </table>
        </div>
    </section>
@endsection