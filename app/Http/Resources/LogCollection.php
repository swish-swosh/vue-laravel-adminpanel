<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class LogCollection extends JsonResource
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
            'user_id' => $this->user_id,
            'user' => $this->user->name,
            'resource_type' => $this->resourceType->name,
            // 'resource_type_group' => $this->resourceTypeGroup->name,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'data' => $this->data
        ];
    }
}
