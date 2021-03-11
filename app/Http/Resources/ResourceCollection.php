<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResourceCollection extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'user_id' => $this->user_id,
            'resource_type_id' => $this->resource_type_id,
            'resource_type' => $this->resourceType->name,
            'resource_type_group' => $this->resourceTypeGroup->name,
            'is_active' => (boolean) $this->is_active,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'data' => $this->data
        ];
    }
}
