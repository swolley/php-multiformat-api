<?php
namespace Api\Core;

abstract class AuthModel {

    public function __construct() { 
        
    }


    /**
     * authenticate than calls canDoRequest to authorize (checks permissions)
     * @param   Request $request        request info
     * @return  boolean                 request authenticate and authorized
     * @uses            canDoRequest
     */
    public static function authorizeRequest(Request &$request) : bool {
        return $request->getToken() !== null && $request->getResource() !== null && $request->getMethod() !== null 
            ? static::canDoRequest($request) 
            : false;
    }

    /**
     * checks user permissions for authorize request
     * @param   Request $request        request info
     * @return  boolean                 request authenticate and authorized
     * @uses            getFromToken
     */
    protected static function canDoRequest(Request &$request) : bool {
        $user = static::getFromToken($request->getToken());
        if( isset($user) ) {
            $permission = "{$request->getMethod()}_{$request->getResource()}";
            return in_array($permission, $user[2]);
        }

        return false;
    }

    /**
     * decrypt token and returns user info and permissions
     * @param   string  $token          user token in request
     * @return  array                   user data inside token
     */
    public static abstract function getFromToken(string $token) : array;

    /**
     * create new Token
     * @param   array   $user           user info
     * @return  string                  new generated token
     */
    public abstract function createToken(array &$user) : string;
}