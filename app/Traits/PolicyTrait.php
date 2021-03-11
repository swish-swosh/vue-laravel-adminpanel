<?php
namespace App\Traits;

use App\Models\AccessRight;
use Illuminate\Support\Facades\Auth;

trait PolicyTrait {

    /**
     * Determine whether the user can update the model.
     *
     * @param  Authenticated User
     * @param  resource to check
     * @param  resource name used in Accessright which contains grants
     * @param  policy to check: view, create, update or delete.
     * @return true/false
     */
    public function checkMyAccessPolicy($user, $resource, $resourceName, $policy){

        if($user->hasAnyRoles(['superAdmin','admin'])) return true;

        // Auth:user roles = resources permitted roles?
        $roles = $user->roles->pluck('name','id')->toArray();
        $access = AccessRight::select('grants')->where('name', $resourceName)->get()->toArray();

        foreach ($roles as $key => $value){
            if(in_array($key, $access[0]['grants']['role'.$key][$policy])) return true; 
        }
        // Auth:user user has no accessrights for this resource
        return false;
    }

    public function grantedByUserAndRole($user, $resource, $resourceName, $policy){

        if($user->hasAnyRoles(['superAdmin','admin'])) return true;
        
        $roles = $user->roles->pluck('id')->toArray();
        return $query->whereIn('role_id', $roles)
            ->orWhere('user_id', $user->id)->get();

        $roles = $user->roles->pluck('name','id')->toArray();
        $access = AccessRight::select('grants')->where('name', $resource)->get()->toArray();

        // check if one of my roles is part of the permitted roles to access this resource.
        foreach ($roles as $key => $value){
            if(in_array($key, $access[0]['grants']['role'.$key][$policy])) return true; 
        } 
        return false;
    }

    public function ImAuthenticatedPolicy($user){
        if (Auth::check()) return true;
        return false;
    }
}