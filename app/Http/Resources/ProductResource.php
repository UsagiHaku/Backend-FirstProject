<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'products',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'price' => $this->price
            ],
            'links' => [
                'self' => route('products.show', [$this->id])
            ]
        ];
    }
}
