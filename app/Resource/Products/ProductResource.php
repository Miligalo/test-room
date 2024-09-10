<?php

namespace App\Resource\Products;

use App\Models\Product;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Product $this */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'currency_id' => $this->currency_id,
        ];
    }
}

