<?php

namespace App\Http\Resources;

use App\Http\Resources\ResourceTypeCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceAccessResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}


