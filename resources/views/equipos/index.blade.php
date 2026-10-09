@extends('layouts.app')

@section('title', 'Equipos')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-sm text-slate-600">Consulta y administra los equipos y su información general.</p>
        <button type="button" data-open-modal="equipo-create" class="rounded-lg bg-teal-700 px-5 py-3 text-sm font-medium text-white hover:bg-teal-800">Registrar equipo</button>
    </div>
    @if (session('success'))
        <div role="status" class="rounded-lg border border-teal-200 bg-teal-50 p-4 text-sm text-teal-800">{{ session('success') }}</div>
    @endif
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-6">
            <h2 class="text-lg font-semibold">Listado de equipos</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $equipos->total() }} equipos registrados</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] text-left text-sm">
                <caption class="sr-only">Listado de equipos</caption>
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        @foreach (['Nombre', 'Código', 'Tipo', 'Ubicación', 'Estado', 'Fecha de registro', 'Acciones'] as $column)
                            <th scope="col" class="px-6 py-4 font-medium">{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($equipos as $equipo)
                        <tr>
                            <td class="max-w-64 break-words px-6 py-4 font-medium">{{ $equipo->nombre }}</td>
                            <td class="px-6 py-4">{{ $equipo->codigo }}</td>
                            <td class="px-6 py-4">{{ $equipo->tipo }}</td>
                            <td class="max-w-64 break-words px-6 py-4">{{ $equipo->ubicacion }}</td>
                            <td class="px-6 py-4">
                                <span @class(['inline-block whitespace-nowrap rounded-full px-3 py-1 text-xs font-medium', 'bg-teal-50 text-teal-800' => $equipo->estado === 'Activo', 'bg-slate-100 text-slate-700' => $equipo->estado !== 'Activo'])>{{ $equipo->estado }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">{{ $equipo->fecha_registro->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <button type="button" data-open-modal="equipo-edit-{{ $equipo->id }}" aria-label="Editar {{ $equipo->nombre }}" class="font-medium text-teal-700 hover:underline">Editar</button>
                                    <button type="button" data-open-modal="equipo-delete-{{ $equipo->id }}" aria-label="Eliminar {{ $equipo->nombre }}" class="font-medium text-red-700 hover:underline">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-12 text-center text-slate-500">Todavía no existen registros de equipos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($equipos->hasPages())
            <div class="border-t border-slate-200 p-6">{{ $equipos->links() }}</div>
        @endif
    </section>
    <div class="contents">
    @include('equipos.modal', ['equipo' => new \App\Models\Equipo])
    @foreach ($equipos as $equipo)
        @include('equipos.modal', ['equipo' => $equipo])
        @include('equipos.delete-modal', ['equipo' => $equipo])
    @endforeach
    @if ($equipoModal && ! $equipos->contains('id', $equipoModal->id))
        @include('equipos.modal', ['equipo' => $equipoModal])
    @endif
    </div>
@endsection
