<?php
namespace Api\Core;

abstract class AuthModel {
    //private $db;

    public function __construct(){ 
        
    }

    public function authorizeRequest(array $request) : bool {
        return isset($request['token'], $request['request'], $request['method']) ? $this->canDoRequest($request) : false;
    }

    protected function canDoRequest(array &$request) : bool {
        $user = $this->getFromToken($request['token']);
        if(isset($user)){
            $permission = "{$request['method']}_{$request['request']}";
            return in_array($permission, json_decode($user[2]));
        }

        return false;
    }

    protected abstract function getFromToken(string &$token) : array;

    public static abstract function createToken(array &$user) : string;
}