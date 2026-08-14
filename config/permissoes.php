<?php

return [

    /*
     * Nome da role de sistema com bypass total (ver Gate::before em AppServiceProvider).
     * Essa role nunca recebe permissões diretamente — é tratada como acesso irrestrito.
     */
    'papel_administrador' => 'Admin',

    /*
     * Catálogo de permissões, agrupado por módulo para a tela de administração
     * de papéis. Convenção de nome: "{entidade}.{acao}".
     *
     * Novos módulos de negócio do solicite devem adicionar seus próprios
     * grupos aqui — o seeder (PermissaoSeeder) sincroniza este catálogo com
     * a tabela `permissions` automaticamente.
     */
    'grupos' => [
        'papeis' => [
            'codigo' => '01',
            'label' => 'Papéis',
            'permissoes' => [
                'papeis.visualizar' => 'Listar e visualizar papéis',
                'papeis.criar' => 'Cadastrar papéis',
                'papeis.editar' => 'Editar papéis e suas permissões',
                'papeis.excluir' => 'Excluir papéis',
            ],
        ],
        'usuarios' => [
            'codigo' => '02',
            'label' => 'Usuários',
            'permissoes' => [
                'usuarios.visualizar' => 'Listar e visualizar usuários',
                'usuarios.gerenciar_permissoes' => 'Atribuir papel e permissões diretas ao usuário',
            ],
        ],
        'pessoas' => [
            'codigo' => '03',
            'label' => 'Pessoas',
            'permissoes' => [
                'pessoas.criar' => 'Cadastrar pessoas',
            ],
        ],
        'solicitacao' => [
            'codigo' => '04',
            'label' => 'Solicitações',
            'permissoes' => [
                'solicitacao.visualizar' => 'Listar e visualizar solicitações',
                'solicitacao.criar' => 'Cadastrar solicitações',
                'solicitacao.cancelar' => 'Cancelar solicitações pendentes',
            ],
        ],
    ],

];
