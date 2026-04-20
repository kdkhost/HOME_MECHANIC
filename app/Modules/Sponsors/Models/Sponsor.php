<?php

namespace App\Modules\Sponsors\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sponsor extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'website',
        'description',
        'animation',
        'speed',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sponsor) {
            if (empty($sponsor->slug)) {
                $sponsor->slug = Str::slug($sponsor->name);
            }
            if (is_null($sponsor->sort_order)) {
                $sponsor->sort_order = static::max('sort_order') + 1;
            }
        });
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) return null;
        if (str_starts_with($this->logo, 'http')) return $this->logo;
        return '/' . ltrim($this->logo, '/');
    }

    public function getAnimationClassAttribute(): string
    {
        $classes = [
            'fade' => 'animate__fadeIn',
            'slide' => 'animate__slideInUp',
            'zoom' => 'animate__zoomIn',
            'flip' => 'animate__flipInX',
            'bounce' => 'animate__bounceIn',
        ];
        return $classes[$this->animation] ?? 'animate__fadeIn';
    }

    public function getSpeedValueAttribute(): string
    {
        $speeds = [
            'slow' => '1.5s',
            'normal' => '1s',
            'fast' => '0.5s',
        ];
        return $speeds[$this->speed] ?? '1s';
    }
}
