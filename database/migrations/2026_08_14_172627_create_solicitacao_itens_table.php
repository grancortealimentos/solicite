<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solicitacao_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitacao_id')->constrained('solicitacoes')->cascadeOnDelete();
            $table->string('codigo', 20);
            $table->string('descricao');
            $table->string('unidade_medida', 10);
            $table->string('armazem', 10);
            $table->string('cta_contabil', 20);
            $table->string('grupo_produto');
            $table->unsignedInteger('quantidade');
            $table->date('data_prazo');
            $table->string('observacao', 500);
            $table->string('centro_custo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitacao_itens');
    }
};
