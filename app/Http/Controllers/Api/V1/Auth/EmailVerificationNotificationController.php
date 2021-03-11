<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = User::where('email',$request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return redirect(env('BASE_URL_FRONTEND') . '/auth/login?status=already validated');
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message'=>'A new password reset link has been send to your email address, please verify!'], 200);
    }
}
