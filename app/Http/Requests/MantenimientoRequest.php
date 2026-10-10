<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MantenimientoRequest extends FormRequest
{
    protected $redirectRoute = 'mantenimientos.index';

    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        $mantenimiento = $this->route('mantenimiento');
        $this->session()->flash('mantenimiento_modal', $mantenimiento ? 'mantenimiento-edit-'.$mantenimiento->id : 'mantenimiento-create');
        $this->session()->flash('mantenimiento_modal_id', $mantenimiento?->id);
        $this->session()->flash('mantenimiento_form_errors', $validator->errors()->toArray());

        parent::failedValidation($validator);
    }

    public function rules(): array
    {
        return [
            'equipo_id' => ['required', 'integer', Rule::exists('equipos', 'id')],
            'tipo' => ['required', Rule::in(['Preventivo', 'Correctivo'])],
            'fecha_programada' => ['required', 'date_format:Y-m-d', 'after_or_equal:1000-01-01', 'before_or_equal:9999-12-31'],
            'descripcion' => ['required', 'string', 'max:5000'],
            'estado' => ['required', Rule::in(['Pendiente', 'En proceso', 'Finalizado'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'equipo_id' => 'equipo',
            'tipo' => 'tipo',
            'fecha_programada' => 'fecha programada',
            'descripcion' => 'descripción',
            'estado' => 'estado',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'integer' => 'Selecciona un equipo válido.',
            'equipo_id.exists' => 'El equipo seleccionado no existe.',
            'in' => 'Selecciona un valor válido para :attribute.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe superar :max caracteres.',
            'fecha_programada.date_format' => 'La fecha programada debe ser una fecha válida.',
            'fecha_programada.after_or_equal' => 'La fecha programada debe ser del año 1000 o posterior.',
            'fecha_programada.before_or_equal' => 'La fecha programada no puede superar el año 9999.',
        ];
    }
}
