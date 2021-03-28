<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Services\SSO;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    private SSO $_sso;

    public function __construct(SSO $sso) {
        $this->_sso = $sso;
    }

    public function auth() {
        return redirect($this->_sso->getAuthUrl());
    }

    public function handleCallback(Request $request) {
        $code = $request->query('code');
        $token = $this->_sso->getToken($code);
        $data = $this->_sso->verify($token['access_token']);

        $character = Character::query()
                              ->whereKey($data['CharacterID'])
                              ->first();

        if (!$character)
        {
            $character       = new Character();
            $character->id   = $data['CharacterID'];
            $character->name = $data['CharacterName'];
        }

        $character->access_token  = $token['access_token'];
        $character->refresh_token = $token['refresh_token'];
        $character->expires       = new Carbon($data['ExpiresOn']);
        $character->esi_scopes    = explode(' ', $data['Scopes']);
        $character->save();

        return redirect('/settings');
    }
}
