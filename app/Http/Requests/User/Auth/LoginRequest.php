<?php

namespace App\Http\Requests\User\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida o formulário de login antes de entregar as credenciais ao AuthController.
 */
class LoginRequest extends FormRequest
{
    /**
     * Autoriza o envio do formulário de login.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza o e-mail para evitar diferenças de caixa e espaços nas credenciais.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->email)) {
            $this->merge(['email' => mb_strtolower(trim($this->email))]);
        }
    }

    /**
     * Define as regras de validação das credenciais.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:50'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Define mensagens de erro em português.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Digite um e-mail válido.',
            'email.max' => 'O e-mail deve ter no máximo 50 caracteres.',
            'password.required' => 'Informe sua senha.',
        ];
    }
}
