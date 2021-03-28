<?php

namespace App\Services;

class SSO {

    private string $_clientID;

    private string $_secret;

    private array $_scopes;

    public function __construct() {
        $this->_clientID = config('esi.client_id');
        $this->_secret = config('esi.secret');
        $this->_scopes = config('esi.scopes');
    }

    public function getAuthUrl(): string {
        $params = http_build_query([
            'response_type' => 'code',
            'redirect_uri'  => route('auth.callback'),
            'client_id'     => $this->_clientID,
            'scope'         => implode(' ', $this->_scopes),
            'state'         => md5(time()),
        ]);

        return "https://login.eveonline.com/oauth/authorize/?{$params}";
    }

    public function getToken(string $code) {
        $headers = [
            'Authorization: Basic ' . base64_encode($this->_clientID . ':' . $this->_secret),
            'Content-Type: application/json',
        ];

        $fields = json_encode([
            'grant_type' => 'authorization_code',
            'code'       => $code,
        ]);

        $ch = curl_init('https://login.eveonline.com/oauth/token');

        curl_setopt_array($ch, [
            CURLOPT_URL             => 'https://login.eveonline.com/oauth/token',
            CURLOPT_POST            => true,
            CURLOPT_POSTFIELDS      => $fields,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_USERAGENT       => config('esi.user_agent'),
            CURLOPT_SSL_VERIFYPEER  => true,
            CURLOPT_SSL_CIPHER_LIST => 'TLSv1',
        ]);

        $result = curl_exec($ch);

        return json_decode($result, true);
    }

    public function verify(string $token) {
        $headers = [
            'Authorization: Bearer ' . $token,
        ];

        $ch = curl_init('https://login.eveonline.com/oauth/verify');

        curl_setopt_array($ch, [
            CURLOPT_URL             => 'https://login.eveonline.com/oauth/verify',
            CURLOPT_POST            => false,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_USERAGENT       => config('esi.user_agent'),
            CURLOPT_SSL_VERIFYPEER  => true,
            CURLOPT_SSL_CIPHER_LIST => 'TLSv1',
        ]);

        $result = curl_exec($ch);

        return json_decode($result, true);
    }
}
