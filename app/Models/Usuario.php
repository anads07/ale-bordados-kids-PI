<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Model de autenticação da tabela oficial "usuarios".
 */
class Usuario extends Authenticatable
{
    protected $table = 'usuarios';

    protected $primaryKey = 'id_us';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'nome_usuario',
        'sobrenome_usuario',
        'email',
        'senha',
        'is_admin',
    ];

    protected $hidden = [
        'senha',
    ];

    protected $casts = [
        'senha' => 'hashed',
        'is_admin' => 'boolean',
    ];

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'usuario', 'id_us');
    }

    // Confirma a flag de admin e o e-mail administrativo configurado.
    public function isAdminAutorizado(): bool
    {
        return $this->is_admin
            && strcasecmp((string) $this->email, (string) config('admin.email')) === 0;
    }

    // Identifica a coluna de senha usada na autenticação.
    public function getAuthPassword(): string
    {
        return $this->senha;
    }

    // Indica ao Laravel a coluna usada no rehash da senha.
    public function getAuthPasswordName(): string
    {
        return 'senha';
    }
}
