<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EquipoRequest extends FormRequest
{
    protected $redirectRoute = 'equipos.index';

    protected function failedValidation(Validator $validator): void
    {
        $equipo = $this->route('equipo');
        $this->session()->flash('equipo_modal', $equipo ? 'equipo-edit-'.$equipo->id : 'equipo-create');
        $this->session()->flash('equipo_modal_id', $equipo?->id);
        $this->session()->flash('equipo_form_errors', $validator->errors()->toArray());

        parent::failedValidation($validator);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $codigoUnico = Rule::unique('equipos', 'codigo');

        if ($equipo = $this->route('equipo')) {
            $codigoUnico->ignore($equipo);
        }

        return [
            'nombre' => ['required', 'string', 'max:255'],
            'codigo' => ['required', 'string', 'max:50', $codigoUnico],
            'tipo' => ['required', 'string', 'max:100'],
            'ubicacion' => ['required', 'string', 'max:255'],
            'estado' => ['required', Rule::in(['Activo', 'Fuera de servicio'])],
            'fecha_registro' => ['required', 'date_format:Y-m-d'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'codigo' => 'código',
            'tipo' => 'tipo',
            'ubicacion' => 'ubicación',
            'estado' => 'estado',
            'fecha_registro' => 'fecha de registro',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe superar :max caracteres.',
            'codigo.unique' => 'Este código ya está registrado.',
            'estado.in' => 'Selecciona un estado válido.',
            'fecha_registro.date_format' => 'La fecha de registro debe ser una fecha válida.',
        ];
    }
}
