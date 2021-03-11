<?php

namespace App\Models;

use App\Models\Role;
use App\Models\ResourceType;
use App\Models\ResourceTypeRole;
use Illuminate\Database\Eloquent\Model;

class ResourceAccess extends Model
{
    // force use this table:
    protected $table="resource_types";

    protected $fillable = [
        'name',
        'description',
        'can_create',
        'can_read',
        'can_update',
        'can_delete',
        'updated_at'
    ];

    /**
     * The attributes that should be cast. (json data via array)
     *
     * @var array
     */
    protected $casts = [
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'resource_type_role', 'resource_type_id', 'role_id' )
            ->withPivot('can_create','can_read',
                    'can_update','can_delete');
    }

    public function resourceType()
    {
        return $this->belongsToMany(ResourceType::class)->using(ResourceTypeRole::class);
    }
}
