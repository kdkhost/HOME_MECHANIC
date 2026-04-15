<?php

namespace App\Modules\Services\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'icon' => $this->icon,
            'cover_image' => $this->cover_image,
            'cover_image_url' => $this->cover_image_url,
            'cover_thumbnail_url' => $this->cover_thumbnail_url,
            'featured' => $this->featured,
            'sort_order' => $this->sort_order,
            'active' => $this->active,
            'uploads_count' => $this->uploads()->count(),
            'created_at' => $this->created_at?->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i'),
            'created_at_iso' => $this->created_at?->toISOString(),
            'updated_at_iso' => $this->updated_at?->toISOString(),
        ];
    }
}