<?php

namespace App\Http\Requests\User\Cart;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Valida a personalização antes de criar uma linha no carrinho.
 */
class AddToCartRequest extends FormRequest
{
    // Autoriza a adição para visitantes e clientes autenticados.
    public function authorize(): bool
    {
        return true;
    }

    // Define as regras de validação da personalização.
    public function rules(): array
    {
        $produto = $this->route('produto');
        $estoque = max(1, (int) ($produto?->estoque ?? 1));
        $personalizacao = config('mock.personalizacao', []);

        return [
            'quantidade' => ['required', 'integer', 'min:1', 'max:'.min($estoque, 99)],
            'cor_produto' => ['required', 'string', 'max:20', Rule::in($personalizacao['cores_produto'] ?? [])],
            'escrita' => ['required', 'string', 'max:40'],
            'cor_escrita' => ['required', 'string', 'max:20', Rule::in($personalizacao['cores_linha'] ?? [])],
            'fonte_escrita' => ['required', 'string', 'max:40', Rule::in($personalizacao['fontes'] ?? [])],
            'desenho' => ['required', 'string', 'max:30'],
            'observacoes' => ['nullable', 'string', 'max:500'],
            'imagem_inspiracao' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    // Define mensagens de erro em português.
    public function messages(): array
    {
        return [
            'quantidade.required' => 'Informe a quantidade desejada.',
            'quantidade.min' => 'A quantidade mínima é 1.',
            'quantidade.max' => 'A quantidade solicitada ultrapassa o estoque disponível.',
            'cor_produto.required' => 'Selecione a cor da peça.',
            'cor_produto.in' => 'Selecione uma cor de peça válida.',
            'escrita.required' => 'Informe o texto do bordado.',
            'escrita.max' => 'O texto do bordado deve ter no máximo 40 caracteres.',
            'cor_escrita.required' => 'Selecione a cor da linha.',
            'cor_escrita.in' => 'Selecione uma cor de linha válida.',
            'fonte_escrita.required' => 'Selecione a fonte do texto.',
            'fonte_escrita.in' => 'Selecione uma fonte válida.',
            'desenho.required' => 'Descreva o desenho desejado.',
            'desenho.max' => 'A descrição do desenho deve ter no máximo 30 caracteres.',
            'observacoes.max' => 'As observações devem ter no máximo 500 caracteres.',
            'imagem_inspiracao.mimes' => 'Envie uma imagem JPG, PNG ou WEBP.',
            'imagem_inspiracao.max' => 'A imagem de inspiração deve ter no máximo 5 MB.',
        ];
    }
}
