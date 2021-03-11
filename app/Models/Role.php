<?php

namespace App\Models;

use App\Models\User;
use App\Models\ResourceType;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable= [
        'name',
        'type'
    ];
    
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function resourceTypes()
    {
        return $this->belongsToMany(ResourceType::class, 'resource_type_role', 'role_id', 'resource_type_id' )->withTimestamps();
    }
}
