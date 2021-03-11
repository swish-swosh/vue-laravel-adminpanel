<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Model;

class ResourceType extends Model
{

    protected $fillable = [
        'name',
        'description'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function resource()
    {
        return $this->hasMany(Resource::class, 'id', 'resource_type_id');
    }
}
