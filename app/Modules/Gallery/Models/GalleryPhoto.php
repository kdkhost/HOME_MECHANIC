<?php

namespace App\Modules\Gallery\Models;

use App\Modules\Upload\Models\Upload;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class GalleryPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'filename',
        'thumbnail',
        'description',
        'sort_order',
        'active'
    ];

    protected $casts = [
        'category_id' => 'integer',
        'sort_order' => 'integer',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com categoria
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(GalleryCategory::class, 'category_id');
    }

    /**
     * Relacionamento com uploads
     */
    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'model');
    }

    /**
     * Obter upload da imagem principal
     */
    public function getMainUpload()
    {
        if (!$this->filename) {
            return null;
        }

        return Upload::where('uuid', $this->filename)->first();
    }

    /**
     * Obter upload do thumbnail
     */
    public function getThumbnailUpload()
    {
        if (!$this->thumbnail) {
            return null;
        }

        return Upload::where('uuid', $this->thumbnail)->first();
    }

    /**
     * Obter URL da imagem principal
     */
    public function getImageUrlAttribute(): ?string
    {
        // Suporte para URLs externas direto no campo filename
        if ($this->filename && str_starts_with($this->filename, 'http')) {
            return $this->filename;
        }

        $upload = $this->getMainUpload();
        return $upload ? $upload->url : null;
    }

    /**
     * Obter URL do thumbnail
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $upload = $this->getThumbnailUpload();
        return $upload ? $upload->url : ($this->getMainUpload()?->thumbnail_url);
    }

    /**
     * Obter dimensões da imagem se disponível
     */
    public function getImageDimensionsAttribute(): ?array
    {
        $upload = $this->getMainUpload();
        if (!$upload || !$upload->exists()) {
            return null;
        }

        try {
            $imagePath = $upload->getFullPath();
            $imageInfo = getimagesize($imagePath);
            
            if ($imageInfo) {
                return [
                    'width' => $imageInfo[0],
                    'height' => $imageInfo[1],
                    'ratio' => round($imageInfo[0] / $imageInfo[1], 2)
                ];
            }
        } catch (\Exception $e) {
            \Log::warning('Erro ao obter dimensões da imagem', [
                'photo_id' => $this->id,
                'filename' => $this->filename,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Scope para fotos ativas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
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
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope para filtrar por categoria
     */
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope para fotos recentes
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Verificar se a foto tem imagem válida
     */
    public function hasValidImage(): bool
    {
        $upload = $this->getMainUpload();
        return $upload && $upload->exists() && $upload->is_image;
    }

    /**
     * Obter foto anterior na mesma categoria
     */
    public function getPreviousPhoto(): ?self
    {
        return static::where('category_id', $this->category_id)
                    ->where('active', true)
                    ->where('sort_order', '<', $this->sort_order)
                    ->orderBy('sort_order', 'desc')
                    ->first();
    }

    /**
     * Obter próxima foto na mesma categoria
     */
    public function getNextPhoto(): ?self
    {
        return static::where('category_id', $this->category_id)
                    ->where('active', true)
                    ->where('sort_order', '>', $this->sort_order)
                    ->orderBy('sort_order', 'asc')
                    ->first();
    }

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Definir sort_order automaticamente dentro da categoria
        static::creating(function ($photo) {
            if (is_null($photo->sort_order)) {
                $maxOrder = static::where('category_id', $photo->category_id)->max('sort_order');
                $photo->sort_order = ($maxOrder ?? 0) + 1;
            }
        });

        // Ao excluir foto, remover associações de uploads
        static::deleting(function ($photo) {
            try {
                // Remover associação do upload principal
                if ($photo->filename) {
                    $upload = Upload::where('uuid', $photo->filename)->first();
                    if ($upload) {
                        $upload->update(['model_type' => null, 'model_id' => null]);
                    }
                }

                // Remover associação do thumbnail se diferente
                if ($photo->thumbnail && $photo->thumbnail !== $photo->filename) {
                    $upload = Upload::where('uuid', $photo->thumbnail)->first();
                    if ($upload) {
                        $upload->update(['model_type' => null, 'model_id' => null]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Erro ao remover associações de upload da foto', [
                    'photo_id' => $photo->id,
                    'error' => $e->getMessage()
                ]);
            }
        });
    }

    /**
     * Serialização para JSON
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        
        // Adicionar URLs e informações computadas
        $array['image_url'] = $this->image_url;
        $array['thumbnail_url'] = $this->thumbnail_url;
        $array['image_dimensions'] = $this->image_dimensions;
        $array['has_valid_image'] = $this->hasValidImage();
        $array['category_name'] = $this->category?->name;
        $array['previous_photo_id'] = $this->getPreviousPhoto()?->id;
        $array['next_photo_id'] = $this->getNextPhoto()?->id;
        
        return $array;
    }
}