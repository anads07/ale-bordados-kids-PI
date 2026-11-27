<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model da tabela oficial de produtos e dos controles de personalização.
 */
class Produto extends Model
{
    protected $table = 'produtos';

    protected $primaryKey = 'id_pr';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'nome_produto',
        'categoria',
        'preco',
        'estoque',
        'descricao',
        'imagem',
        'permite_texto',
        'permite_cor',
        'permite_fonte',
    ];

    // Tipa preço, estoque e os controles de personalização.
    protected $casts = [
        'preco' => 'decimal:2',
        'estoque' => 'integer',
        'permite_texto' => 'boolean',
        'permite_cor' => 'boolean',
        'permite_fonte' => 'boolean',
    ];

    // Lista os pedidos que usam este produto.
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'produto', 'id_pr');
    }

    // Resolve a URL da imagem real do produto quando o arquivo existe.
    public function getImagemUrlAttribute(): ?string
    {
        if ($this->imagem && file_exists(public_path('images/produtos/'.$this->imagem))) {
            return asset('images/produtos/'.$this->imagem);
        }

        return null;
    }

    // Formata o preço no padrão brasileiro.
    public function getPrecoFormatadoAttribute(): string
    {
        return 'R$ '.number_format((float) $this->preco, 2, ',', '.');
    }

    // Nota simulada compartilhada pelos cartões enquanto as avaliações não são reais.
    public function getNotaAttribute(): float
    {
        return (float) config('mock.rating.nota', 4.5);
    }

    // Quantidade simulada de avaliações exibida ao lado das estrelas.
    public function getTotalAvaliacoesAttribute(): int
    {
        return (int) config('mock.rating.total', 23);
    }
}
