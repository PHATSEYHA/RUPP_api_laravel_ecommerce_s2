<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'thumbnail' => asset('storage/' . $this->thumbnail),
            'desc' => $this->desc,
            'price' => $this->price,
            'stock' => $this->stock,
            'category' => [
                'id' => optional($this->category)->id,
                'name' => optional($this->category)->name,
            ]
        ];
    }
}
