<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Model da tabela oficial "pedidos": cada linha representa um produto
 * personalizado finalizado pelo cliente e vinculado ao usuário autenticado.
 */
class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $primaryKey = 'id_pedido';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'usuario',
        'produto',
        'quantidade',
        'cor_produto',
        'escrita',
        'cor_escrita',
        'fonte_escrita',
        'desenho',
        'observacoes',
        'imagem_inspiracao',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'forma_pagamento',
        'data_pedido',
        'status',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'data_pedido' => 'date',
    ];

    /**
     * Relaciona o pedido ao usuário responsável pela compra. O nome evita
     * conflito com a coluna "usuario", que guarda apenas o ID no banco.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario', 'id_us');
    }

    /**
     * Relaciona o pedido ao produto personalizado. O nome evita conflito com
     * a coluna "produto", que guarda apenas o ID no banco.
     */
    public function produtoRelacionado(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'produto', 'id_pr');
    }

    /**
     * Resolve a URL pública da imagem de inspiração salva no disco public.
     */
    public function getImagemInspiracaoUrlAttribute(): ?string
    {
        if (! $this->imagem_inspiracao) {
            return null;
        }

        if (! Storage::disk('public')->exists($this->imagem_inspiracao)) {
            return null;
        }

        return Storage::disk('public')->url($this->imagem_inspiracao);
    }

    /**
     * Monta o endereço de entrega em uma linha legível para o painel.
     */
    public function getEnderecoFormatadoAttribute(): string
    {
        $cidadeEstado = collect([$this->cidade, $this->estado])
            ->filter()
            ->implode('/');

        $partes = array_filter([
            $this->endereco,
            $this->numero,
            $this->complemento,
            $this->bairro,
            $cidadeEstado,
            $this->cep ? 'CEP '.$this->cep : null,
        ], fn (?string $valor) => filled($valor));

        return $partes ? implode(', ', $partes) : 'Não informado';
    }

    /**
     * Retorna o rótulo e a classe centralizados no mapa de status do projeto.
     */
    public function getStatusConfigAttribute(): array
    {
        return config('mock.status.'.$this->status, [
            'label' => ucfirst((string) $this->status),
            'class' => 'badge-cancelado',
        ]);
    }

    /**
     * Monta as quatro etapas visuais do pedido usadas nos cartões de gerenciamento.
     */
    public function getEtapasAttribute(): array
    {
        $configuracao = config('mock.admin.order_stages', []);
        $etapasConcluidas = $configuracao['by_status'][$this->status] ?? ['cadastrado'];

        return collect($configuracao['labels'] ?? [])
            ->map(fn (string $rotulo, string $etapa) => [
                'key' => $etapa,
                'label' => $rotulo,
                'done' => in_array($etapa, $etapasConcluidas, true),
            ])
            ->values()
            ->all();
    }

    /**
     * Calcula o valor total da linha do pedido com base no preço do produto.
     */
    public function getPrecoTotalFormatadoAttribute(): string
    {
        $preco = (float) ($this->produtoRelacionado?->preco ?? 0);

        return 'R$ '.number_format($preco * (int) $this->quantidade, 2, ',', '.');
    }
}
