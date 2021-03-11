<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleAccessCollection extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'access' => $this->whenPivotLoaded('resource_type_role', function () {
                return [
                //    'restrict_organisation' => (boolean) $this->pivot->restrict_organisation,
                //    'can_admin_own' => (boolean) $this->pivot->can_admin_own,
                    'can_create' => (boolean) $this->pivot->can_create,
                    'can_read' => (boolean) $this->pivot->can_read,
                    'can_update' => (boolean) $this->pivot->can_update,
                    'can_delete' => (boolean) $this->pivot->can_delete
                ];
            })
        ];
    }
}
