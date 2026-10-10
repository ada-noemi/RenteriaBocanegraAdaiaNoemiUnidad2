@php
    $editing = $equipo->exists;
    $modalId = $editing ? 'equipo-edit-'.$equipo->id : 'equipo-create';
    $modalErrors = new \Illuminate\Support\MessageBag(session('equipo_form_errors', []));
    $preserveInput = session('equipo_modal') === $modalId && $modalErrors->any();
    $formErrors = $preserveInput ? $modalErrors : new \Illuminate\Support\MessageBag;
@endphp
<dialog id="{{ $modalId }}" aria-labelledby="{{ $modalId }}-title"
        @if (session('equipo_modal') === $modalId) data-auto-open @endif
        class="m-auto max-h-[90dvh] w-[calc(100%-2rem)] max-w-2xl overflow-y-auto rounded-xl border border-slate-200 bg-white p-0 text-slate-800 shadow-xl backdrop:bg-slate-950/60">
    <div class="flex items-center justify-between gap-4 border-b border-slate-200 p-6">
        <h2 id="{{ $modalId }}-title" class="text-xl font-semibold">{{ $editing ? 'Editar equipo' : 'Registrar equipo' }}</h2>
        <button type="button" data-close-modal aria-label="Cerrar formulario" class="rounded-lg px-3 py-2 text-slate-500 hover:bg-slate-100">✕</button>
    </div>
    <form action="{{ $editing ? route('equipos.update', $equipo) : route('equipos.store') }}" method="POST" class="p-6">
        @csrf
        @if ($editing) @method('PUT') @endif
        @include('equipos.form', ['buttonLabel' => $editing ? 'Guardar cambios' : 'Registrar equipo'])
    </form>
</dialog>
