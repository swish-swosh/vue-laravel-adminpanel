<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class LogResource extends JsonResource
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
            'resource_type' => $this->resourceType->name,
            'resource_type_group' => $this->resourceTypeGroup->name,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'data' => $this->data
        ];
    }
}
