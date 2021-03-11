<?php

namespace App\Http\Resources;
use App\Models\Resource;
use App\Http\Resources\RoleIdCollection;
use App\Http\Resources\UserProfileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    
    public static $wrap = 'user';

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
            'roles' => RoleIdCollection::collection($this->roles)
        ];
    }
}
