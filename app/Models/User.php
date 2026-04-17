<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
        ];
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
}
