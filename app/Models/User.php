<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'autentique_user_id',
    'autentique_person_id',
    'name',
    'email',
    'is_active',
    'force_password_change',
    'email_verified_at',
    'autentique_synced_at',
])]
#[Hidden(['remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'force_password_change' => 'boolean',
            'email_verified_at' => 'datetime',
            'autentique_synced_at' => 'datetime',
        ];
    }

    /**
     * @return HasOne<UserSetting, $this>
     */
    public function setting(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    /**
     * Atribui um único papel ao usuário, substituindo qualquer papel anterior
     * (regra de negócio: um usuário tem no máximo um papel por vez).
     */
    public function assignSingleRole(Role|string $role): void
    {
        $this->syncRoles([$role]);
    }

    /**
     * Cria ou atualiza o espelho local a partir do payload de usuário da API autentique.
     */
    public static function syncFromAutentique(array $payload): self
    {
        return self::updateOrCreate(
            ['autentique_user_id' => $payload['id']],
            [
                'autentique_person_id' => $payload['person_id'] ?? null,
                'name' => $payload['name'],
                'email' => $payload['email'],
                'is_active' => $payload['is_active'] ?? true,
                'force_password_change' => $payload['force_password_change'] ?? false,
                'autentique_synced_at' => now(),
            ]
        );
    }
}
