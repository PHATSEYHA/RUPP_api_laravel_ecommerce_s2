<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Setup format
        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
        ];

        $numPro = $request->filled('num_pro') ? intval($request->input('num_pro')) : 0;
        if ($numPro == 1) {
            $response['product_count'] = $this->products_count;
        }

        return $response;
    }
}
