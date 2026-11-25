<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

// Valida os dados enviados pelo formulário de produtos.
class ProdutoRequest extends FormRequest
{
    // Confirma que somente o administrador autorizado envie o formulário.
    public function authorize(): bool
    {
        $usuario = $this->user();
        $emailAdministrativo = (string) config('admin.email');

        return (bool) $usuario?->is_admin
            && strcasecmp((string) $usuario->email, $emailAdministrativo) === 0;
    }

    // Define as regras de cadastro e edição.
    public function rules(): array
    {
        $editando = $this->route('produto') !== null;

        return [
            'nome_produto' => ['required', 'string', 'max:50'],
            'categoria' => ['required', 'string', 'max:50'],
            'descricao' => ['nullable', 'string', 'max:5000'],
            'preco' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'estoque' => ['required', 'integer', 'min:0', 'max:999999'],
            'imagem' => [
                $editando ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'extensions:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }

    // Define mensagens de validação em português.
    public function messages(): array
    {
        return [
            'nome_produto.required' => 'Informe o nome do produto.',
            'nome_produto.max' => 'O nome do produto deve ter no máximo 50 caracteres.',
            'categoria.required' => 'Informe a categoria do produto.',
            'categoria.max' => 'A categoria deve ter no máximo 50 caracteres.',
            'preco.required' => 'Informe o preço do produto.',
            'preco.numeric' => 'O preço deve ser um valor numérico.',
            'preco.min' => 'O preço não pode ser negativo.',
            'estoque.required' => 'Informe o estoque disponível.',
            'estoque.integer' => 'O estoque deve ser um número inteiro.',
            'estoque.min' => 'O estoque não pode ser negativo.',
            'imagem.required' => 'Envie a imagem principal do produto.',
            'imagem.image' => 'O arquivo enviado precisa ser uma imagem.',
            'imagem.mimes' => 'Envie uma imagem JPG, JPEG, PNG ou WEBP.',
            'imagem.extensions' => 'A extensão do arquivo deve ser JPG, JPEG, PNG ou WEBP.',
            'imagem.max' => 'A imagem deve ter no máximo 5 MB.',
        ];
    }

    // Usa nomes claros nas mensagens de validação.
    public function attributes(): array
    {
        return [
            'nome_produto' => 'nome do produto',
            'categoria' => 'categoria',
            'descricao' => 'descrição',
            'preco' => 'preço',
            'estoque' => 'estoque',
            'imagem' => 'imagem principal',
        ];
    }
}
