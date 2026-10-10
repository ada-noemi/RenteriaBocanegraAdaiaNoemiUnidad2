@if ($formErrors->any())
    <div role="alert" class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">Revisa los campos indicados. Tus datos se conservaron.</div>
@endif
<div class="grid gap-6 md:grid-cols-2">
    @php
        $values = [
            'equipo_id' => $mantenimiento->equipo_id ?? '',
            'tipo' => $mantenimiento->tipo ?? 'Preventivo',
            'estado' => $mantenimiento->estado ?? 'Pendiente',
            'fecha_programada' => $mantenimiento->fecha_programada?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'descripcion' => $mantenimiento->descripcion ?? '',
        ];
    @endphp
    @foreach (['equipo_id' => 'Equipo', 'tipo' => 'Tipo', 'estado' => 'Estado'] as $field => $label)
        <div>
            <label for="{{ $modalId }}-{{ $field }}" class="mb-2 block text-sm font-medium">{{ $label }} <span class="text-red-600">*</span></label>
            <select id="{{ $modalId }}-{{ $field }}" name="{{ $field }}" required
                    @if ($formErrors->has($field)) aria-invalid="true" aria-describedby="{{ $modalId }}-{{ $field }}-error" @endif
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200">
                @if ($field === 'equipo_id')
                    <option value="">Selecciona un equipo</option>
                    @foreach ($equiposDisponibles as $equipoDisponible)
                        <option value="{{ $equipoDisponible->id }}" @selected((string) ($preserveInput ? old($field, $values[$field]) : $values[$field]) === (string) $equipoDisponible->id)>{{ $equipoDisponible->nombre }} — {{ $equipoDisponible->codigo }}</option>
                    @endforeach
                @else
                    @foreach ($field === 'tipo' ? ['Preventivo', 'Correctivo'] : ['Pendiente', 'En proceso', 'Finalizado'] as $option)
                        <option value="{{ $option }}" @selected(($preserveInput ? old($field, $values[$field]) : $values[$field]) === $option)>{{ $option }}</option>
                    @endforeach
                @endif
            </select>
            @if ($formErrors->has($field)) <p id="{{ $modalId }}-{{ $field }}-error" class="mt-2 text-sm text-red-700">{{ $formErrors->first($field) }}</p> @endif
        </div>
    @endforeach
    <div>
        <label for="{{ $modalId }}-fecha_programada" class="mb-2 block text-sm font-medium">Fecha programada <span class="text-red-600">*</span></label>
        <input id="{{ $modalId }}-fecha_programada" name="fecha_programada" type="date" min="1000-01-01" max="9999-12-31" required
               value="{{ $preserveInput ? old('fecha_programada', $values['fecha_programada']) : $values['fecha_programada'] }}"
               @if ($formErrors->has('fecha_programada')) aria-invalid="true" aria-describedby="{{ $modalId }}-fecha_programada-error" @endif
               class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200">
        @if ($formErrors->has('fecha_programada')) <p id="{{ $modalId }}-fecha_programada-error" class="mt-2 text-sm text-red-700">{{ $formErrors->first('fecha_programada') }}</p> @endif
    </div>
    <div class="md:col-span-2">
        <label for="{{ $modalId }}-descripcion" class="mb-2 block text-sm font-medium">Descripción <span class="text-red-600">*</span></label>
        <textarea id="{{ $modalId }}-descripcion" name="descripcion" rows="4" required maxlength="5000"
                  @if ($formErrors->has('descripcion')) aria-invalid="true" aria-describedby="{{ $modalId }}-descripcion-error" @endif
                  class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200">{{ $preserveInput ? old('descripcion', $values['descripcion']) : $values['descripcion'] }}</textarea>
        @if ($formErrors->has('descripcion')) <p id="{{ $modalId }}-descripcion-error" class="mt-2 text-sm text-red-700">{{ $formErrors->first('descripcion') }}</p> @endif
    </div>
</div>
<p class="mt-6 text-xs text-slate-500">Los campos marcados con * son obligatorios.</p>
<div class="mt-6 flex flex-wrap gap-3">
    <button type="submit" @disabled($equiposDisponibles->isEmpty()) class="rounded-lg bg-teal-700 px-5 py-3 text-sm font-medium text-white hover:bg-teal-800 disabled:cursor-not-allowed disabled:bg-slate-400">{{ $buttonLabel }}</button>
    <button type="button" data-close-modal class="rounded-lg border border-slate-300 px-5 py-3 text-sm font-medium hover:bg-slate-50">Cancelar</button>
</div>
