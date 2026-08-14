<?php

namespace App\Models;

use Database\Factories\SolicitacaoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'filial', 'status'])]
class Solicitacao extends Model
{
    /** @use HasFactory<SolicitacaoFactory> */
    use HasFactory;

    public const string STATUS_PENDENTE = 'pendente';

    public const string STATUS_APROVADA = 'aprovada';

    public const string STATUS_REJEITADA = 'rejeitada';

    public const string STATUS_CANCELADA = 'cancelada';

    protected $table = 'solicitacoes';

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(SolicitacaoItem::class);
    }
}
