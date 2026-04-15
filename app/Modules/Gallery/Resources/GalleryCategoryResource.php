<?php

namespace App\Modules\Gallery\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sort_order' => $this->sort_order,
            'active_photos_count' => $this->active_photos_count,
            'total_photos_count' => $this->total_photos_count,
            'cover_photo' => $this->when($this->cover_photo, function () {
                return new GalleryPhotoResource($this->cover_photo);
            }),
            'created_at' => $this->created_at?->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i'),
            'created_at_iso' => $this->created_at?->toISOString(),
            'updated_at_iso' => $this->updated_at?->toISOString(),
        ];
    }
}