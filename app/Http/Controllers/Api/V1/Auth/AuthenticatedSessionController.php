<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Exception;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\GetTokensTrait;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Auth\LoginRequest;
use Laravel\Passport\Client as OAuthClient;

class AuthenticatedSessionController extends Controller
{
    use GetTokensTrait;

    /**
     * USER LOGIN
     * Handle an incoming authentication request.
     * after successful login, tokens are used for secure OAUTH communication
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return response()->json
     */
    public function store(LoginRequest $request) {

        // LoginRequest handles validations and rate limits.
        $request->authenticate();

        // check if user is verified
        if($request->user()->hasVerifiedEmail() == false) {
            return response()->json(['message' => 'You must verify your email address first (send to your email)'], 403);
        }

        // passport token handling!
        $oAuthClient = OAuthClient::where('password_client', 1)->first();
        return $this->getTokenAndRefreshToken($oAuthClient, request('email'), request('password'));      
    }

    /**
     * USER LOGOUT
     * Only authenticated users can successfully log out
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $request->user()->token()->revoke();    // accesstoken (Passport OAUTH2)
        $request->session()->invalidate();      // session
        return response()->json(['message' => 'You are succesfully logged out'], 200);
    }
}
