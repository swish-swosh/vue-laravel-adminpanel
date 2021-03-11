<?php

namespace App\Traits;

use GuzzleHttp\Client;
use Laravel\Passport\Client as OAuthClient;

trait GetTokensTrait
{
    public function getTokenAndRefreshToken(OAuthClient $oAuthClient, $email, $password) { 
        $oAuthClient = OAuthClient::where('password_client', 1)->first();
        $http = new Client;
        $response = $http->request('POST', route('passport.token'), [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => $oAuthClient->id,
                'client_secret' => $oAuthClient->secret,
                'username' => $email,
                'password' => $password,
                'scope' => '*',
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }
}