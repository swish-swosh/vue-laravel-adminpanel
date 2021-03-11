<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use Exception;
use Illuminate\Support\Facades\Auth;

use App\Models\Role;
use App\Models\User; 
use App\Models\Resource;
use App\Models\ResourceType;
use App\Models\ResourceTypeGroup;
use App\Models\Organisation;

use GuzzleHttp\Client;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserDestroyManyRequest;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;

use Laravel\Passport\Client as OAuthClient;

class UserController extends Controller
{

    public function retrieveUser() { 

        $user = Auth::user();

        // add resource data
        $resource = Resource::where('user_id', $user->id)
            ->whereHas('resourceType', function ($q) {
            $q->whereIn('name', ['UserProfile']);
        })->first();

        try { // no data available? set default.
            $userImage = $resource->data['user_image'];
        } catch (Exception $e) {
            $userImage = '';
        }

        return (new UserResource($user))->additional(['profile' => [
            'resource_id' => $resource->id,
            'user_image' => $userImage,
        ]]);
    }
    
    /**
     * Remove the specified resource from storage.DEtach piv
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response(['message'=>'User deleted']);
    }

    /**
     * Remove many resources from storage.
     * destroying user will cascade delete user_profile (user_profile constraint)
     *
     * @param  array  $data->array of id's
     * @return \Illuminate\Http\Response
     */
    public function destroyMany(UserDestroyManyRequest $request, User $user)
    {
        // ADD security for none resource controllers parts !!
        User::destroy(collect($request->ids));
        return response(['message'=>'User(s) deleted']);
    }

}
