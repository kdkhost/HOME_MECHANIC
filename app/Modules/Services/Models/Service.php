<?php

namespace App\Modules\Services\Models;

use App\Modules\Upload\Models\Upload;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'icon',
        'cover_image',
        'featured',
        'sort_order',
        'active'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['cover_image_url'];

    /**
     * Relacionamento com uploads
     */
    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'model');
    }

    /**
     * Obter URL da imagem de capa — path direto em public/uploads/services/
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if (!$this->cover_image) return null;
        
        // Se for uma URL externa, retorna direto
        if (str_starts_with($this->cover_image, 'http')) {
            return $this->cover_image;
        }

        // Se for UUID do FilePond (sem barra), resolver para path real
        if (!str_contains($this->cover_image, '/')) {
            $upload = Upload::where('uuid', $this->cover_image)->first();
            if ($upload) {
                return '/' . ltrim($upload->path, '/');
            }
            return null; // UUID inválido — imagem não encontrada
        }

        return '/' . ltrim($this->cover_image, '/');
    }

    /**
     * Obter URL do thumbnail (mesma imagem — sem processamento separado)
     */
    public function getCoverThumbnailUrlAttribute(): ?string
    {
        return $this->cover_image_url;
    }

    /**
     * Scope para serviços ativos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para serviços em destaque
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope para ordenação
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('title', 'asc');
    }

    /**
     * Scope para busca
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
        });
    }

    /**
     * Gerar slug único
     */
    public static function generateSlug(string $title, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($title);
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
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Gerar slug automaticamente
        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = static::generateSlug($service->title);
            }
        });

        static::updating(function ($service) {
            if ($service->isDirty('title') && empty($service->slug)) {
                $service->slug = static::generateSlug($service->title, $service->id);
            }
        });

        // Definir sort_order automaticamente
        static::creating(function ($service) {
            if (is_null($service->sort_order)) {
                $service->sort_order = static::max('sort_order') + 1;
            }
        });
    }

    /**
     * Serialização para JSON — inclui cover_image_url sempre
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        $array['cover_image_url']       = $this->cover_image_url;
        $array['cover_thumbnail_url']   = $this->cover_thumbnail_url;
        return $array;
    }
}