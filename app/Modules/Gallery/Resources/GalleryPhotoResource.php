<?php

namespace App\Modules\Gallery\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryPhotoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category_name' => $this->category?->name,
            'title' => $this->title,
            'filename' => $this->filename,
            'thumbnail' => $this->thumbnail,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
            'active' => $this->active,
            'image_url' => $this->image_url,
            'thumbnail_url' => $this->thumbnail_url,
            'image_dimensions' => $this->image_dimensions,
            'has_valid_image' => $this->has_valid_image,
            'previous_photo_id' => $this->previous_photo_id,
            'next_photo_id' => $this->next_photo_id,
            'created_at' => $this->created_at?->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i'),
            'created_at_iso' => $this->created_at?->toISOString(),
            'updated_at_iso' => $this->updated_at?->toISOString(),
        ];
    }
}