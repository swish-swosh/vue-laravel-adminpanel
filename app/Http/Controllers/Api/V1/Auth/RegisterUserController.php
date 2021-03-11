<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;

use App\Models\Role;
use App\Models\User;
use App\Models\Resource;
use App\Models\Organisation;
use App\Models\ResourceType;
use App\Models\ResourceTypeGroup;

use App\Traits\GetTokensTrait;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

use App\Http\Requests\Auth\RegisterUserRequest;
use Laravel\Passport\Client as OAuthClient;

class RegisterUserController extends Controller
{
    /**
     * CREATE USER
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\RegisterUserRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __invoke(RegisterUserRequest $request)
    {
        // set default user origanisation
        $organisation = Organisation::select('id')->firstWhere('name', 'default');

        Auth::login($user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'organisation_id' => $organisation->id,
            'password' => Hash::make($request->password),
        ]));

        // API doesn't use event(new Registered($user));  
        $user->sendEmailVerificationNotification();     // register user - send email notification

        // set new user default role to 'Guests' which is the lowest access. 
        // admin should upgrade if needed.
        // sync roles to use this.
        $role = Role::select('id')->firstWhere('name', 'Guests');
        $user->roles()->sync([$role->id]);

        // prepare user profile resource 
        // get id of UserProfile type
        $resourceType=ResourceType::select('id')->firstWhere('name', 'UserProfile');

        // prepare resource type group membership, set to none (default)
        // used to group different resources to be used in tables, ... like monitors
        // get id of ResourceTypeGroup
        $resourceTypeGroup=ResourceTypeGroup::select('id')->firstWhere('name', 'None');

        // add new Resource model for additional data
        $userProfileResource = Resource::create([
            'user_id' => $user->id,
            'resource_type_id' => $resourceType->id,
            'resource_type_group_id' => $resourceTypeGroup->id,
            'is_active' => 1
        ]);
        $userProfileResource->save();

        return response()->json(['message'=>'A verification link has been send to your email address, please verify before login!'], 200);
    }

}
