<?php

namespace App\Modules\Upload\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Upload extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uuid',
        'original_name',
        'filename',
        'mime_type',
        'size',
        'disk',
        'path',
        'thumbnail_path',
        'model_type',
        'model_id'
    ];

    protected $casts = [
        'size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento polimórfico com modelo associado
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Obter URL pública do arquivo
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Obter URL do thumbnail
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_path) {
            return null;
        }

        return Storage::disk($this->disk)->url($this->thumbnail_path);
    }

    /**
     * Verificar se é uma imagem
     */
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Verificar se é um vídeo
     */
    public function getIsVideoAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    /**
     * Verificar se é um documento
     */
    public function getIsDocumentAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'application/');
    }

    /**
     * Obter tamanho formatado
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Obter tipo de arquivo para exibição
     */
    public function getFileTypeAttribute(): string
    {
        return match (true) {
            $this->is_image => 'Imagem',
            $this->is_video => 'Vídeo',
            $this->is_document => 'Documento',
            default => 'Arquivo'
        };
    }

    /**
     * Obter ícone baseado no tipo
     */
    public function getIconAttribute(): string
    {
        return match (true) {
            $this->is_image => 'bi-image',
            $this->is_video => 'bi-play-circle',
            $this->mime_type === 'application/pdf' => 'bi-file-pdf',
            $this->is_document => 'bi-file-text',
            default => 'bi-file'
        };
    }

    /**
     * Verificar se arquivo existe no disco
     */
    public function exists(): bool
    {
        return Storage::disk($this->disk)->exists($this->path);
    }

    /**
     * Obter caminho completo do arquivo
     */
    public function getFullPath(): string
    {
        return Storage::disk($this->disk)->path($this->path);
    }

    /**
     * Scope para filtrar por tipo MIME
     */
    public function scopeByMimeType($query, string $mimeType)
    {
        return $query->where('mime_type', $mimeType);
    }

    /**
     * Scope para filtrar imagens
     */
    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    /**
     * Scope para filtrar vídeos
     */
    public function scopeVideos($query)
    {
        return $query->where('mime_type', 'like', 'video/%');
    }

    /**
     * Scope para filtrar documentos
     */
    public function scopeDocuments($query)
    {
        return $query->where('mime_type', 'like', 'application/%');
    }

    /**
     * Scope para uploads órfãos (sem modelo associado)
     */
    public function scopeOrphaned($query)
    {
        return $query->whereNull('model_type')->whereNull('model_id');
    }

    /**
     * Scope para uploads associados a um modelo
     */
    public function scopeForModel($query, string $modelType, int $modelId)
    {
        return $query->where('model_type', $modelType)->where('model_id', $modelId);
    }

    /**
     * Scope para uploads recentes
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope para uploads por usuário
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Ao excluir upload, remover arquivos do disco
        static::deleting(function ($upload) {
            try {
                // Excluir arquivo principal
                if ($upload->exists()) {
                    Storage::disk($upload->disk)->delete($upload->path);
                }

                // Excluir thumbnail se existir
                if ($upload->thumbnail_path && Storage::disk($upload->disk)->exists($upload->thumbnail_path)) {
                    Storage::disk($upload->disk)->delete($upload->thumbnail_path);
                }
            } catch (\Exception $e) {
                \Log::error('Erro ao excluir arquivos do upload', [
                    'upload_id' => $upload->id,
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
        
        // Adicionar atributos computados
        $array['url'] = $this->url;
        $array['thumbnail_url'] = $this->thumbnail_url;
        $array['formatted_size'] = $this->formatted_size;
        $array['file_type'] = $this->file_type;
        $array['icon'] = $this->icon;
        $array['is_image'] = $this->is_image;
        $array['is_video'] = $this->is_video;
        $array['is_document'] = $this->is_document;
        
        return $array;
    }
}