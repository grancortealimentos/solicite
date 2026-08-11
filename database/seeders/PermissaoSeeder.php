<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissaoSeeder extends Seeder
{
    /**
     * Sincroniza o catálogo de config/permissoes.php com a tabela `permissions`
     * e garante a existência da role de sistema (Admin), sem atribuir
     * permissões a ela — o bypass é feito via Gate::before.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        DB::transaction(function () {
            foreach (config('permissoes.grupos', []) as $grupo) {
                foreach ($grupo['permissoes'] as $nome => $descricao) {
                    Permission::firstOrCreate(['name' => $nome, 'guard_name' => 'web']);
                }
            }

            Role::firstOrCreate([
                'name' => config('permissoes.papel_administrador'),
                'guard_name' => 'web',
            ]);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
