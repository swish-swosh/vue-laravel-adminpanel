<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Exception;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Laravel\Passport\Client as OAuthClient;

class RefreshTokenController extends Controller
{
    public function __invoke(Request $request) { 

        $refresh_token = $request->header('refreshToken');
        $oAuthClient = OAuthClient::where('password_client', 1)->first();
        $http = new Client;

        try {
            $response = $http->request('POST', route('passport.token'), [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refresh_token,
                    'client_id' => $oAuthClient->id,
                    'client_secret' => $oAuthClient->secret,
                    'scope' => '*',
                ],
            ]);
            return json_decode((string) $response->getBody(), true);
        } catch (Exception $e) {
            return response()->json(['error'=>'Unauthorized, please check credentials....'], 401); 
        }
    }
}
