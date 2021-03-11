<?php

namespace App\Http\Controllers\Api\V1\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\Auth\PasswordRecoveryRequest;

class EmailPasswordRecoveryController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\PasswordRecoveryRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __invoke(PasswordRecoveryRequest $request)
    {

        // check if user is verified
        if($request->user()->hasVerifiedEmail() == false) {
            return response()->json(['message' => 'You must verify your email address first (check your email)'], 403);
        }

        // We will send the password reset link to this user.
        Password::sendResetLink(
            $request->only('email')
        );

        return response()->json(['message'=>'A password reset link has been send to your email address, please check your mail and confirm'], 200);
    }
}
