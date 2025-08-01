<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Auth;

class GoogleOAuthController extends Controller
{
    public function redirect()
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/credentials.json'));
        $client->setRedirectUri(route('google.oauth.callback'));
        $client->addScope('https://www.googleapis.com/auth/calendar');
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/credentials.json'));
        $client->setRedirectUri(route('google.oauth.callback'));

        if ($request->has('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($request->input('code'));
            if (isset($token['access_token'])) {
                // Save token to user
                $user = Auth::user();
                $user->google_access_token = $token['access_token'];
                $user->google_refresh_token = $token['refresh_token'] ?? null;
                $user->google_token_expires = now()->addSeconds($token['expires_in'] ?? 3600);
                $user->save();

                return redirect('/')->with('success', 'Google account connected!');
            } else {
                return redirect('/')->with('error', 'Failed to connect Google account.');
            }
        }
        return redirect('/')->with('error', 'No code returned from Google.');
    }
}
