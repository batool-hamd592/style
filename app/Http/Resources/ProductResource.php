<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'currency' => 'JOD',
            'audience' => $this->audience,
            'is_featured' => $this->is_featured,

            'image_url' => $this->image
                ? asset(Storage::url($this->image))
                : null,

            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ],

            'total_stock' => (int) (
                $this->variants_sum_stock
                ?? $this->variants->sum('stock')
            ),

            'variants' => $this->whenLoaded(
                'variants',
                fn () => $this->variants->map(
                    fn ($variant) => [
                        'id' => $variant->id,
                        'sku' => $variant->sku,
                        'size' => $variant->size,
                        'color' => $variant->color,
                        'stock' => $variant->stock,
                        'available' => $variant->stock > 0,
                    ]
                )
            ),

            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}