<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Notifications\ResetPasswordCustom;
use App\Notifications\VerifyEmailCustom;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * Enviar a notificação de redefinição de senha personalizada.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordCustom($token));
    }

    /**
     * Enviar a notificação de verificação de e-mail personalizada.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailCustom);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'phone',
        'bio',
        'email_verified_at',
    ];

    /**
     * URL do avatar — compatível com CloudLinux/CageFS
     * Salvo em public/uploads/avatars/ — acesso direto via asset()
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) return null;

        // Usa URL relativa para evitar problema com APP_URL incorreto no servidor
        return '/' . ltrim($this->avatar, '/');
    }

    /**
     * Inicial do nome para avatar fallback
     */
    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', trim($this->name));
        if (count($parts) >= 2) {
            return strtoupper($parts[0][0] . $parts[count($parts) - 1][0]);
        }
        return strtoupper(substr($this->name, 0, 2));
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permission_level' => 'integer',
        ];
    }

    /**
     * Verificar se é superadmin (nível 100 ou role admin).
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin' || $this->permission_level >= 100;
    }

    /**
     * Verificar se pode gerenciar um usuário específico (hierarquia).
     */
    public function canManageUser(User $targetUser): bool
    {
        // Não pode gerenciar a si mesmo
        if ($this->id === $targetUser->id) {
            return false;
        }

        // Superadmin pode gerenciar todos exceto outros superadmins
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Usuário comum só pode gerenciar se tiver nível maior
        return $this->permission_level > $targetUser->permission_level;
    }

    /**
     * Verificar se pode atribuir uma permissão específica.
     * Só pode atribuir permissões de nível igual ou inferior ao seu.
     */
    public function canAssignPermission(Permission $permission): bool
    {
        // Superadmin pode atribuir qualquer permissão
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Só pode atribuir permissões de nível <= seu nível
        return $permission->level <= $this->permission_level;
    }

    /**
     * Obter o nível máximo de permissão que este usuário pode ter/gerenciar.
     */
    public function getMaxPermissionLevel(): int
    {
        if ($this->isSuperAdmin()) {
            return 100;
        }

        return $this->permission_level;
    }

    /**
     * Verificar se o usuário é administrador
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Relacionamento com uploads
     */
    public function uploads()
    {
        return $this->hasMany(\App\Modules\Upload\Models\Upload::class);
    }

    /**
     * Relacionamento com audit logs
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Relacionamento com permissoes.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission')
                    ->withPivot('granted_at', 'granted_by');
    }

    /**
     * Verificar se usuario possui uma permissao especifica.
     * Administradores sempre tem todas as permissoes.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Administradores tem acesso total
        if ($this->isAdmin()) {
            return true;
        }

        // Verificar permissao ativa atribuida ao usuario
        return $this->permissions()
                    ->where('slug', $permissionSlug)
                    ->where('is_active', true)
                    ->exists();
    }

    /**
     * Verificar se usuario possui permissao para um modulo/acao.
     */
    public function canModule(string $module, string $action): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->permissions()
                    ->where('module', $module)
                    ->where('action', $action)
                    ->where('is_active', true)
                    ->exists();
    }

    /**
     * Verificar se usuario pode acessar um modulo (qualquer acao).
     */
    public function canAccessModule(string $module): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->permissions()
                    ->where('module', $module)
                    ->where('is_active', true)
                    ->exists();
    }

    /**
     * Conceder permissao ao usuario.
     */
    public function grantPermission(int $permissionId, ?int $grantedBy = null): void
    {
        $this->permissions()->syncWithoutDetaching([
            $permissionId => ['granted_at' => now(), 'granted_by' => $grantedBy]
        ]);
    }

    /**
     * Revogar permissao do usuario.
     */
    public function revokePermission(int $permissionId): void
    {
        $this->permissions()->detach($permissionId);
    }

    /**
     * Sincronizar todas as permissoes do usuario.
     */
    public function syncPermissions(array $permissionIds, ?int $grantedBy = null): void
    {
        $syncData = [];
        foreach ($permissionIds as $id) {
            $syncData[$id] = ['granted_at' => now(), 'granted_by' => $grantedBy];
        }
        $this->permissions()->sync($syncData);
    }
}
