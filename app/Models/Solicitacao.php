<?php

namespace App\Models;

use Database\Factories\SolicitacaoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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

    /**
     * Data de emissão + prazo de entrega configurado para o solicitante.
     * Retorna null quando o solicitante não tem prazo configurado.
     */
    public function previsaoEntrega(): ?Carbon
    {
        $leadDays = $this->solicitante?->setting?->delivery_lead_days;

        if ($leadDays === null) {
            return null;
        }

        return $this->created_at->copy()->addDays($leadDays);
    }
}
