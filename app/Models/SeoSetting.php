<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_type',
        'page_identifier',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'custom_head_tags',
        'schema_markup',
        'canonical_url',
        'index',
        'follow'
    ];

    protected $casts = [
        'index' => 'boolean',
        'follow' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Scope para filtrar por tipo de página
     */
    public function scopeByPageType($query, string $pageType)
    {
        return $query->where('page_type', $pageType);
    }

    /**
     * Scope para páginas específicas
     */
    public function scopeByPage($query, string $pageType, ?string $pageIdentifier = null)
    {
        return $query->where('page_type', $pageType)
                    ->where('page_identifier', $pageIdentifier);
    }

    /**
     * Obter robots meta content
     */
    public function getRobotsAttribute(): string
    {
        $robots = [];
        
        $robots[] = $this->index ? 'index' : 'noindex';
        $robots[] = $this->follow ? 'follow' : 'nofollow';
        
        return implode(', ', $robots);
    }

    /**
     * Verificar se tem configurações personalizadas
     */
    public function hasCustomSettings(): bool
    {
        return !empty($this->meta_title) || 
               !empty($this->meta_description) || 
               !empty($this->og_title) || 
               !empty($this->og_description);
    }

    /**
     * Obter preview das meta tags
     */
    public function getPreview(): array
    {
        return [
            'title' => $this->meta_title ?: $this->og_title,
            'description' => $this->meta_description ?: $this->og_description,
            'image' => $this->og_image ?: $this->twitter_image,
            'url' => $this->canonical_url
        ];
    }
}