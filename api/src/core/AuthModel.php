<?php
namespace Api\Core;

abstract class AuthModel {

    public function __construct(){ 
        
    }


    /**
     * authenticate than calls canDoRequest to authorize (checks permissions)
     * @param   Request $request        request info
     * @return  boolean                 request authenticate and authorized
     * @link            canDoRequest    checks for permissions after token authentication
     */
    public function authorizeRequest(Request &$request) : bool {
        return $request->getToken() !== null && $request->getResource() !== null && $request->getMethod() !== null 
            ? $this->canDoRequest($request) 
            : false;
    }

    /**
     * checks user permissions for authorize request
     * @param   Request $request        request info
     * @return  boolean                 request authenticate and authorized
     * @link            getFromToken    get user info encrypted inside token
     */
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

    /**
     * decrypt token and returns user info and permissions
     * @param   string  $token          user token in request
     * @return  array                   user data inside token
     */
    protected abstract function getFromToken(string &$token) : array;

    /**
     * create new Token
     * @param   array   $user           user info
     * @return  string                  new generated token
     */
    public static abstract function createToken(array &$user) : string;
}