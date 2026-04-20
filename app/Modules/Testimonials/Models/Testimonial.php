<?php

namespace App\Modules\Testimonials\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'photo',
        'content',
        'rating',
        'is_active',
        'sort_order',
        'source',
        'email',
        'author_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating'    => 'integer',
        'sort_order'=> 'integer',
    ];

    protected $appends = ['photo_url'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Resolver URL do avatar: Google photo > User cadastrado > Iniciais
     */
    public function getPhotoUrlAttribute(): ?string
    {
        // Se tem foto do Google, usar ela
        if ($this->photo && str_starts_with($this->photo, 'http')) {
            return $this->photo;
        }

        // Se tem foto local (upload), resolver path
        if ($this->photo && !str_starts_with($this->photo, 'http')) {
            return '/' . ltrim($this->photo, '/');
        }

        // Se tem email, buscar avatar do usuario cadastrado com mesmo email
        if ($this->email) {
            $user = User::where('email', $this->email)->first();
            if ($user && $user->avatar) {
                return str_starts_with($user->avatar, 'http')
                    ? $user->avatar
                    : '/' . ltrim($user->avatar, '/');
            }
        }

        // Sem foto — retorna null (view usa iniciais como fallback)
        return null;
    }
}
