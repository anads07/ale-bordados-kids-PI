<?php

namespace App\Http\Requests\User\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Valida o cadastro e divide o nome entre as colunas do schema oficial.
 */
class RegisterRequest extends FormRequest
{
    // Autoriza o envio do formulário de cadastro.
    public function authorize(): bool
    {
        return true;
    }

    // Remove espaços excedentes e padroniza o e-mail.
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nome_completo' => is_string($this->nome_completo) ? trim($this->nome_completo) : $this->nome_completo,
            'email' => is_string($this->email) ? mb_strtolower(trim($this->email)) : $this->email,
        ]);
    }

    // Define as regras de validação do cadastro.
    public function rules(): array
    {
        return [
            'nome_completo' => ['required', 'string', 'max:70'],
            'email' => ['required', 'string', 'email', 'max:50', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    // Confere se o nome tem duas partes e respeita os limites do banco.
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->has('nome_completo')) {
                return;
            }

            [$nome, $sobrenome] = $this->partesDoNome();

            if ($nome === '') {
                $validator->errors()->add('nome_completo', 'Informe seu nome completo.');

                return;
            }

            if ($sobrenome === '') {
                $validator->errors()->add('nome_completo', 'Informe nome e sobrenome.');

                return;
            }

            if (mb_strlen($nome) > 20) {
                $validator->errors()->add('nome_completo', 'O primeiro nome deve ter no máximo 20 caracteres.');
            }

            if (mb_strlen($sobrenome) > 50) {
                $validator->errors()->add('nome_completo', 'O sobrenome deve ter no máximo 50 caracteres.');
            }
        });
    }

    /**
     * Divide o nome na primeira palavra e no restante.
     *
     * @return array{0: string, 1: string}
     */
    public function partesDoNome(): array
    {
        $partes = preg_split('/\s+/u', trim((string) $this->nome_completo)) ?: [];

        if (count($partes) < 2) {
            return [$partes[0] ?? '', ''];
        }

        return [
            array_shift($partes),
            implode(' ', $partes),
        ];
    }

    // Define mensagens de erro em português.
    public function messages(): array
    {
        return [
            'nome_completo.required' => 'Informe seu nome completo.',
            'nome_completo.max' => 'O nome completo deve ter no máximo 70 caracteres.',
            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Digite um e-mail válido.',
            'email.max' => 'O e-mail deve ter no máximo 50 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.required' => 'Informe uma senha.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
        ];
    }
}
