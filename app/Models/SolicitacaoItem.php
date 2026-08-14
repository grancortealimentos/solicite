<?php

namespace App\Models;

use Database\Factories\SolicitacaoItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'solicitacao_id',
    'codigo',
    'descricao',
    'unidade_medida',
    'armazem',
    'cta_contabil',
    'grupo_produto',
    'quantidade',
    'data_prazo',
    'observacao',
    'centro_custo',
])]
class SolicitacaoItem extends Model
{
    /** @use HasFactory<SolicitacaoItemFactory> */
    use HasFactory;

    protected $table = 'solicitacao_itens';

    protected function casts(): array
    {
        return [
            'data_prazo' => 'date',
        ];
    }

    public function solicitacao(): BelongsTo
    {
        return $this->belongsTo(Solicitacao::class);
    }
}
