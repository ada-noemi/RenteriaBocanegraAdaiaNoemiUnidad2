<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    protected $redirectRoute = 'login';

    protected function failedValidation(Validator $validator): void
    {
        $this->session()->flash('login_errors', $validator->errors()->toArray());
        parent::failedValidation($validator);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Ingresa tu correo electrónico.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.string' => 'Ingresa un correo electrónico válido.',
            'email.max' => 'El correo electrónico no debe superar 255 caracteres.',
            'password.required' => 'Ingresa tu contraseña.',
            'password.string' => 'Ingresa una contraseña válida.',
            'password.max' => 'La contraseña no debe superar 255 caracteres.',
        ];
    }
}
