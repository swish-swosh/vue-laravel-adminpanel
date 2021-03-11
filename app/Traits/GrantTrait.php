<?php
namespace App\Traits;
use App\Models\AccessRight;
use App\Models\ResourceAccess;

trait GrantTrait {

    public function checkGranted($myAccess, $myRoles, $myResource=false){

        // myAccess = 'can_create, can_read, can_update, can_delete'
        // myRoles = array with obtained roles (role names)
        // $myResource = if available, return true/false if access for specific resource is available (one),
        // if false, return id's of resource types having access (all)
        // example: $myAccess = "can_read", $myRoles = ['Authors', 'Guests'], $myResource = "Monitors"

        if($myResource==false){
            return ResourceAccess::whereHas('roles', function ($q) use ($myAccess, $myRoles) {
                $q->whereIn('name', $myRoles) 
                    ->where($myAccess, 1);             
            })->get()->pluck('id')->toArray(); 
        }
        
        return ResourceAccess::where('name', $myResource)
            ->whereHas('roles', function ($q) use ($myAccess, $myRoles) {
                $q->whereIn('name', $myRoles)
            ->where($myAccess, 1);
        })->exists() == 1;

    }
}