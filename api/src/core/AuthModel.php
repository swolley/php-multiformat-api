<?php
namespace Api\Core;

abstract class AuthModel {
    //private $db;

    public function __construct(){ 
        
    }

    public function authorizeRequest(array $request) : bool {

        return isset($request['token'], $request['resource'], $request['method']) ? $this->canDoRequest($request) : false;
    }

    protected function canDoRequest(array &$request) : bool {
        $authData = explode(" ", $request['token']);
        
        //authorize only configured auth method
        if($authData[0] !== ucfirst(AUTH_METHOD)){
            return false;
        }

        //parse token using method defined by user
        $user = $this->getFromToken($authData[1]);
        if(isset($user)){
            $permission = "{$request['method']}_{$request['resource']}";
            return in_array($permission, json_decode($user[2]));
        }

        return false;
    }

    protected abstract function getFromToken(string &$token) : array;

    public static abstract function createToken(array &$user) : string;
}