<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ResourceType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResourceTypePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return Auth::check();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceType  $resourceType
     * @return mixed
     */
    public function view(User $user, ResourceType $resourceType)
    {
        return Auth::check();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAnyRoles(['administrators']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Resource  $resourceType
     * @return mixed
     */
    public function update(User $user, ResourceType $resourceType)
    {
        return $user->hasAnyRoles(['administrators']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceType  $resourceType
     * @return mixed
     */ 
    public function delete(User $user, ResourceType $resourceType)
    {
        return $user->hasAnyRoles(['administrators']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceType  $resourceType
     * @return mixed
     */
    public function restore(User $user, ResourceType $resourceType)
    {
        return $user->hasAnyRoles(['administrators']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceType  $resourceType
     * @return mixed
     */
    public function forceDelete(User $user, ResourceType $resourceType)
    {
        return $user->hasAnyRoles(['administrators']);
    }
}
