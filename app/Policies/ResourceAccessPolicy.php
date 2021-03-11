<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ResourceAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResourceAccessPolicy
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
     * @param  \App\Models\ResourceAccess $resourceAccess
     * @return mixed
     */
    public function view(User $user, ResourceAccess $resourceAccess)
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
        return $user->hasAnyRoles(['administrators', 'editors', 'viewers']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceAccess $resourceAccess
     * @return mixed
     */
    public function update(User $user, ResourceAccess $resourceAccess)
    {
        return $user->hasAnyRoles(['administrators', 'editors', 'viewers']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceAccess $resourceAccess
     * @return mixed
     */ 
    public function delete(User $user, ResourceAccess $resourceAccess)
    {
        return $user->hasAnyRoles(['administrators', 'editors', 'viewers']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceAccess $resourceAccess
     * @return mixed
     */
    public function restore(User $user, ResourceAccess $resourceAccess)
    {
        return $user->hasAnyRoles(['administrators', 'editors', 'viewers']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceAccess $resourceAccess
     * @return mixed
     */
    public function forceDelete(User $user, ResourceAccess $resourceAccess)
    {
        return $user->hasAnyRoles(['administrators', 'editors', 'viewers']);
    }
}
