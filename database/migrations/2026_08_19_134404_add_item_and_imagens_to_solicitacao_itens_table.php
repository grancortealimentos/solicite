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
        Schema::table('solicitacao_itens', function (Blueprint $table) {
            $table->unsignedInteger('item')->nullable()->after('solicitacao_id');
            $table->json('imagens')->nullable()->after('centro_custo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitacao_itens', function (Blueprint $table) {
            $table->dropColumn(['item', 'imagens']);
        });
    }
};
