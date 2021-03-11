<?php

namespace App\Models;

use App\Models\User;
use App\Models\LogType;

use App\Traits\GrantTrait;
use App\Traits\CanBeScoped;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Log extends Model
{
    use CanBeScoped, GrantTrait;

    protected $fillable = [
        'resource_type_id',
        'resource_type_group_id',
        'user_id',
        'is_active',
        'updated_at',
        'data'
    ];

    /**
     * The attributes that should be cast. (data = json = uses array)
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime:Y-m-d H:i:s'
      //  'created_at' => 'datetime:Y-m-d',
    ];

    public function user()
    {
       return $this->hasOne(User::class, 'id', 'user_id' );
    }

    public function resourceType()
    {
        return $this->hasOne(ResourceType::class, 'id', 'resource_type_id' );
    }

    public function resourceTypeGroup()
    {
        return $this->hasOne(ResourceTypeGroup::class, 'id', 'resource_type_group_id' );
    }

    /**
     * Scope a query to only include data of certain type(s).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $types
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfGrantedType($query, $grant)
    {
        $id = Auth::id();
        $user = User::findOrFail($id);
        $myRoles = $user->pluckRoles(['name']);
        $resourceTypes = $this->checkGranted($grant, $myRoles);
        return $query->whereIn('resource_type_id', $resourceTypes);
    }
}
