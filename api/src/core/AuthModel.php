<?php
namespace Api\Core;

abstract class AuthModel {
    //private $db;

    public function __construct(){ 
        
    }

    public function authorizeRequest(Request &$request) : bool {

        return $request->getToken() !== null && $request->getResource() !== null && $request->getMethod() !== null ? $this->canDoRequest($request) : false;
    }

    protected function canDoRequest(Request &$request) : bool {
        $auth_data = explode(" ", $request->getToken());
        
        //authorizes only configured auth method
        if($auth_data[0] !== ucfirst(AUTH_METHOD)){
            return false;
        }

        //parse token using method defined by user
        $user = $this->getFromToken($auth_data[1]);
        if(isset($user)){
            $permission = "{$request->getMethod()}_{$request->getResource()}";
            return in_array($permission, json_decode($user[2]));
        }

        return false;
    }

    protected abstract function getFromToken(string &$token) : array;

    public static abstract function createToken(array &$user) : string;
}