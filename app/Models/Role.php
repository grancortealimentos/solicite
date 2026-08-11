<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function isSystemRole(): bool
    {
        return $this->name === config('permissoes.papel_administrador');
    }
}
