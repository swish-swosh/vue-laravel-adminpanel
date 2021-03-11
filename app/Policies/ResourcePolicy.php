<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Resource;

use App\Traits\GrantTrait;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResourcePolicy
{
    use HandlesAuthorization, GrantTrait;

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
        // need to be logged in, access can not be checked here, ofGrantedType Trait used for individual checks ( details = controller )
        return Auth::check();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Resource  $resource
     * @return mixed
     */
    public function view(User $user, Resource $resource)
    {
       if($user->id === $resource->user_id) return true;  // owner? ok to view

       // check if I'm granted for 'can_read', $myRoles, resourceType
       return $this->checkGranted('can_read', $user->pluckRoles(['name']), $resource->resourceType->name );
    }

    /**
     * Determine whether the user can create the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
       return $this->checkGranted('can_create', $user->pluckRoles(['name']));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Resource  $resource
     * @return mixed
     */
    public function update(User $user, Resource $resource)
    {
       return $this->checkGranted('can_update', $user->pluckRoles(['name']), $resource->resourceType->name );
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Resource  $resource
     * @return mixed
     */ 
    public function delete(User $user, Resource $resource)
    {
       return $this->checkGranted('can_delete', $user->pluckRoles(['name']), $resource->resourceType->name );
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Resource  $resource
     * @return mixed
     */
    public function restore(User $user, Resource $resource)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Resource  $resource
     * @return mixed
     */
    public function forceDelete(User $user, Resource $resource)
    {
        return false;
    }

    /**
     * Determine whether the user can delete many models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function deleteMany(User $user)
    {
        return $user->hasAnyRoles(['Admins']);
    }
}
