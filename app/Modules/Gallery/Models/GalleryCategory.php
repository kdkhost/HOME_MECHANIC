<?php

namespace App\Modules\Gallery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GalleryCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'active',
        'sort_order'
    ];

    protected $casts = [
        'active'     => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com fotos da galeria
     */
    public function photos(): HasMany
    {
        return $this->hasMany(GalleryPhoto::class, 'category_id');
    }

    /**
     * Relacionamento com fotos ativas
     */
    public function activePhotos(): HasMany
    {
        return $this->photos()->where('active', true);
    }

    /**
     * Scope para ordenação
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Scope para busca
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Gerar slug único
     */
    public static function generateSlug(string $name, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = static::where('slug', $slug);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (!$query->exists()) {
                break;
            }

            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Obter contagem de fotos ativas
     */
    public function getActivePhotosCountAttribute(): int
    {
        return $this->activePhotos()->count();
    }

    /**
     * Obter primeira foto como capa
     */
    public function getCoverPhotoAttribute(): ?GalleryPhoto
    {
        return $this->activePhotos()->ordered()->first();
    }

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Gerar slug automaticamente
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = static::generateSlug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = static::generateSlug($category->name, $category->id);
            }
        });

        // Definir sort_order automaticamente
        static::creating(function ($category) {
            if (is_null($category->sort_order)) {
                $category->sort_order = static::max('sort_order') + 1;
            }
        });
    }

    /**
     * Serialização para JSON
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        
        // Adicionar contadores e relacionamentos
        $array['active_photos_count'] = $this->active_photos_count;
        $array['total_photos_count'] = $this->photos()->count();
        $array['cover_photo'] = $this->cover_photo;
        
        return $array;
    }
}