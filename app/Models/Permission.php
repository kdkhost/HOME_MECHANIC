<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'module',
        'action',
        'level',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'level' => 'integer',
    ];

    /**
     * Usuarios que possuem esta permissao.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permission')
                    ->withPivot('granted_at', 'granted_by');
    }

    /**
     * Scope para permissoes ativas.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope por modulo.
     */
    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope para nivel minimo.
     */
    public function scopeMinLevel($query, int $level)
    {
        return $query->where('level', '<=', $level);
    }

    /**
     * Verificar se permissao pertence a um modulo.
     */
    public function belongsToModule(string $module): bool
    {
        return $this->module === $module;
    }
}
