<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\Auth\PasswordResetRequest;

class PasswordResetController extends Controller
{
    /**
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\PasswordResetRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __invoke(PasswordResetRequest $request)
    {
        // Here we will attempt to reset the user's password. If it is successful we
        // will reset the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {

                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
                
                event(new PasswordReset($user));
            }
        );

        // Password::PASSWORD_RESET = passwords.reset if updated, passwords.token if expired or wrong token
        // If the password was successfully reset, return 200, otherwise 403
        return $status == Password::PASSWORD_RESET ? 
            response()->json(['message'=>'Password update successful!'], 200 ):
            response()->json(['error'=>'Update failed, retry the password update procedure or contact support.'], 403 );        
    }
}
