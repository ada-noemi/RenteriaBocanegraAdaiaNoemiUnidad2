@if ($formErrors->any())
    <div role="alert" class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
        Revisa los campos indicados. Tus datos se conservaron.
    </div>
@endif
<div class="grid gap-6 md:grid-cols-2">
    @foreach (['nombre' => ['Nombre', 255], 'codigo' => ['Código único', 50], 'tipo' => ['Tipo', 100], 'ubicacion' => ['Ubicación', 255]] as $field => [$label, $length])
        <div>
            <label for="{{ $modalId }}-{{ $field }}" class="mb-2 block text-sm font-medium">{{ $label }} <span class="text-red-600">*</span></label>
            <input id="{{ $modalId }}-{{ $field }}" name="{{ $field }}" type="text" required maxlength="{{ $length }}"
                   value="{{ $preserveInput ? old($field, $equipo->$field) : $equipo->$field }}"
                   @if ($formErrors->has($field)) aria-invalid="true" aria-describedby="{{ $modalId }}-{{ $field }}-error" @endif
                   class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200">
            @if ($formErrors->has($field))
                <p id="{{ $modalId }}-{{ $field }}-error" class="mt-2 text-sm text-red-700">{{ $formErrors->first($field) }}</p>
            @endif
        </div>
    @endforeach
    <div>
        <label for="{{ $modalId }}-estado" class="mb-2 block text-sm font-medium">Estado <span class="text-red-600">*</span></label>
        <select id="{{ $modalId }}-estado" name="estado" required
                @if ($formErrors->has('estado')) aria-invalid="true" aria-describedby="{{ $modalId }}-estado-error" @endif
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200">
            @foreach (['Activo', 'Fuera de servicio'] as $estado)
                <option value="{{ $estado }}" @selected(($preserveInput ? old('estado', $equipo->estado ?? 'Activo') : ($equipo->estado ?? 'Activo')) === $estado)>{{ $estado }}</option>
            @endforeach
        </select>
        @if ($formErrors->has('estado')) <p id="{{ $modalId }}-estado-error" class="mt-2 text-sm text-red-700">{{ $formErrors->first('estado') }}</p> @endif
    </div>
    <div>
        <label for="{{ $modalId }}-fecha_registro" class="mb-2 block text-sm font-medium">Fecha de registro <span class="text-red-600">*</span></label>
        <input id="{{ $modalId }}-fecha_registro" name="fecha_registro" type="date" required
               value="{{ $preserveInput ? old('fecha_registro', $equipo->fecha_registro?->format('Y-m-d') ?? now()->format('Y-m-d')) : ($equipo->fecha_registro?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
               @if ($formErrors->has('fecha_registro')) aria-invalid="true" aria-describedby="{{ $modalId }}-fecha_registro-error" @endif
               class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200">
        @if ($formErrors->has('fecha_registro')) <p id="{{ $modalId }}-fecha_registro-error" class="mt-2 text-sm text-red-700">{{ $formErrors->first('fecha_registro') }}</p> @endif
    </div>
</div>
<p class="mt-6 text-xs text-slate-500">Los campos marcados con * son obligatorios.</p>
<div class="mt-6 flex flex-wrap gap-3">
    <button type="submit" class="rounded-lg bg-teal-700 px-5 py-3 text-sm font-medium text-white hover:bg-teal-800">{{ $buttonLabel }}</button>
    <button type="button" data-close-modal class="rounded-lg border border-slate-300 px-5 py-3 text-sm font-medium hover:bg-slate-50">Cancelar</button>
</div>
