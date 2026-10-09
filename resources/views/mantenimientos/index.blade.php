@extends('layouts.app')
@section('title', 'Mantenimientos')
@section('content')
    <p class="text-sm text-slate-600">Consulta las actividades de mantenimiento de los equipos.</p>
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-6">
            <h2 class="text-lg font-semibold">Listado de mantenimientos</h2>
            <p class="mt-1 text-sm text-slate-500">El registro, edición y eliminación se habilitarán en la siguiente etapa de este módulo.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-left text-sm">
                <caption class="sr-only">Listado de mantenimientos</caption>
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        @foreach (['Equipo', 'Tipo', 'Fecha programada', 'Descripción', 'Estado'] as $column)
                            <th scope="col" class="px-6 py-4 font-medium">{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody><tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Todavía no existen registros de mantenimientos.</td></tr></tbody>
            </table>
        </div>
    </section>
@endsection