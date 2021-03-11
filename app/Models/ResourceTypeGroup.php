<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Model;

class ResourceTypeGroup extends Model
{

    protected $fillable = [
        'name'
    ];

    public function resource()
    {
        return $this->hasMany(Resource::class, 'id', 'resource_type_id');
    }
}
