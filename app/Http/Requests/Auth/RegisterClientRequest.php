<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:client',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'O nome é obrigatório.',
            'name.max'           => 'O nome não pode ter mais de 255 caracteres.',
            'email.required'     => 'O e-mail é obrigatório.',
            'email.email'        => 'Introduza um endereço de e-mail válido.',
            'email.unique'       => 'Este e-mail já está registado.',
            'password.required'  => 'A senha é obrigatória.',
            'password.min'       => 'A senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'As senhas não coincidem.',
        ];
    }
}
