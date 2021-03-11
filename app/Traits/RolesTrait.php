<?php
namespace App\Traits;

use App\Models\AccessRight;

trait RolesTrait {

    public function checkResourcePolicy($user, $resource, $policy){

        $roles = $user->roles->pluck('name','id')->toArray();
        $access = AccessRight::select('grants')->where('name', $resource)->get()->toArray();

        // check if one of my roles is part of the resource roles permitted to access.
        foreach ($roles as $key => $value){
            if(in_array($key, $access[0]['grants']['role'.$key][$policy])) return true; 
        } 
        return false;
    }
}