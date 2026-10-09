<dialog id="equipo-delete-{{ $equipo->id }}" aria-labelledby="equipo-delete-{{ $equipo->id }}-title"
        class="m-auto max-h-[90dvh] w-[calc(100%-2rem)] max-w-md overflow-y-auto rounded-xl bg-white p-6 text-slate-800 shadow-xl backdrop:bg-slate-950/60">
    <h2 id="equipo-delete-{{ $equipo->id }}-title" class="text-xl font-semibold">Eliminar equipo</h2>
    <p class="mt-4 break-words text-sm text-slate-600">¿Deseas eliminar «{{ $equipo->nombre }}»? Esta acción no se puede deshacer.</p>
    <form action="{{ route('equipos.destroy', $equipo) }}" method="POST" class="mt-6 flex flex-wrap gap-3">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-lg bg-red-700 px-5 py-3 text-sm font-medium text-white hover:bg-red-800">Eliminar equipo</button>
        <button type="button" data-close-modal class="rounded-lg border border-slate-300 px-5 py-3 text-sm font-medium hover:bg-slate-50">Cancelar</button>
    </form>
</dialog>
