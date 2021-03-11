<?php

namespace App\Policies;

use App\Models\Log;
use App\Models\User;
use App\Traits\GrantTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class LogPolicy
{
    use HandlesAuthorization, GrantTrait;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        // check function view for details
        return Auth::check();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Log  $log
     * @return mixed
     */
    public function view(User $user, Log $log)
    {
        if($user->id === $log->user_id) return true;  // owner? ok to view
        return $this->checkGranted('can_view', $user->pluckRoles(['name']), $log->resourceType->name );
    }

    /**
     * Determine whether the user can create models.
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
     * @param  \App\Models\Log  $resource
     * @return mixed
     */
    public function update(User $user, Log $log)
    {
        return $this->checkGranted('can_update', $user->pluckRoles(['name']), $log->resourceType->name );
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Log  $log
     * @return mixed
     */ 
    public function delete(User $user, Log $log)
    {
        return $this->checkGranted('can_delete', $user->pluckRoles(['name']), $log->resourceType->name );
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Log  $log
     * @return mixed
     */
    public function restore(User $user, Log $log)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Log  $log
     * @return mixed
     */
    public function forceDelete(User $user, Log $log)
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
