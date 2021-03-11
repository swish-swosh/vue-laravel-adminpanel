<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;

use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{

    use VerifiesEmails;

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request  //@@1
     * @return \Illuminate\Http\RedirectResponse
     */
   // public function __invoke($user_id, Request $request)
    public function __invoke($user_id, Request  $request)
    {
        // route contains signed middleware, so it will trow a error when the signature is expired or incorrect
        // cannot use Auth because we don't want the verification confirmation link to contain bearer token
        // which is necessary for Auth to work.

        $user = User::findOrFail($user_id);

        if ($user->hasVerifiedEmail()) {
            // validation is OK, inform user, load login modal
            return redirect(env('BASE_URL_FRONTEND') . '/auth/login?status=already validated&email=' . $user->email);
        }
        
        if ($request->hasValidSignature()) {
            // mail signature valid, mark as verified via event, goto login
            $user->markEmailAsVerified();
            return redirect(env('BASE_URL_FRONTEND') . "/auth/login?status=You're validated&email=" . $user->email);
        } 
            // signature invalid, verification link expired
            return redirect(env('BASE_URL_FRONTEND') . '/auth/verification-expired?email=' . $user->email);
    }
}
