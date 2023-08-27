<?php

namespace App\Core\Support;

use Exception;
use App\Core\Response;
use App\Core\Application;

class Csrf
{
    
    public $appSession;
    protected $tokenKey = '_csrf_token';
    protected $tokenTimestampKey = '_csrf_token_timestamp';
    protected $tokenExpiration = 60; //1800; // Token expiration time in seconds (30 minutes)

    public function __construct()
    {
        $this->appSession = Application::$app->session;
        if (!$this->appSession->exists($this->tokenKey) || !$this->appSession->exists($this->tokenTimestampKey)) {
            $this->regenerateToken();
        }
    }

    public function regenerateToken()
    {
        $this->appSession->set($this->tokenKey, $this->generateRandomToken());
        $this->appSession->set($this->tokenTimestampKey, time());
    }

    private function generateRandomToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function getToken()
    {
        $this->regenerateToken();
        return $this->appSession->get($this->tokenKey);
    }

    public function getTokenTimestamp()
    {
        return $this->appSession->get($this->tokenTimestampKey);
    }

    public function verifyToken($userToken)
    {
        $storedToken = $this->appSession->get($this->tokenKey);
        return $userToken === $storedToken;
    }

    public function checkToken()
    {
        $userToken = Application::$app->request->post($this->tokenKey);

        if ($this->appSession->exists($this->tokenKey) &&
            $this->appSession->exists($this->tokenTimestampKey) &&
            $this->verifyToken($userToken) &&
            $this->isTokenValid()) {
            return true;
        }

        abort(Response::FORBIDDEN);
    }

    private function isTokenValid()
    {
        $tokenTimestamp = $this->getTokenTimestamp();
        $currentTime = time();
        return ($currentTime - $tokenTimestamp) <= $this->tokenExpiration;
    }

}
