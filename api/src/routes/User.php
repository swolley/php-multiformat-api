<?php
namespace Api\Routes;
use Api\Core\RouteModel;
use Api\Core\Request;
use Api\Core\HttpStatusCode;
use Api\Local\Auth;

class User extends RouteModel {

    public function  __construct(){
        parent::__construct();
    }

    public function put(Request &$request) {
        throw new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);
    } 

    public function get(Request &$request) {
        $params = $request->getParameters();
        
        $result = isset($params['id']) ? 
                $this->db->procedure('GetUser', ['id' => $params['id']]) : 
                $this->db->procedure('GetAllUsers');

        return [
            'rowId' => 'id',    //TODO: don't like so much this solution to responde primary key
            'data' => isset($params['id']) && count($result) === 1
                ? $result[0]
                : $result
        ];
    }

    public function post(Request &$request) {
        $params = $request->getParameters();
        
        //check if user exists
        if(!isset($params['email'], $params['password'])){
            throw new \BadMethodCallException("Missing parameters", HttpStatusCode::BAD_REQUEST);
        }

        if(strlen($params['email']) === 0 || !filter_var($params['email'], FILTER_VALIDATE_EMAIL) || strlen($params['password']) === 0){
            throw new \InvalidArgumentException("Invalid parameters vaules", HttpStatusCode::BAD_REQUEST);
        }

        $user = $this->db->procedure('GetUserByCredentials', [
            'email' => $params['email'],
            'hashedPassword' => hash('sha256', $params['password'])
        ]);

        if(count($user) === 0){
            return [];
        }

        $auth = new Auth();
        $auth::createAccessToken($user[0]);  //sets header, no need to returns it
        $refreshToken = $auth::createRefreshToken($user[0]);

        $this->db->procedure("InsertUserToken", [
            "userId" => $user[0]['id'],
            "token" => $refreshToken
        ]);

        return [
            'data' => $user
        ]; 
    }

    public function delete(Request &$request) {
        $token = $request->getToken();
        
        if(!isset($token)){
            throw new \BadMethodCallException("Missing parameters", HttpStatusCode::BAD_REQUEST);
        }

        $result = $this->db->procedure('DeleteUserTokens', [
            'token' => $token
        ]);

        return $result != 0 ? [
            'data' => 'logged out'
        ] : 'No token found';
    }

    public function patch(Request &$request) {
        $token = $request->getToken();
        $auth = new Auth();
        $userInToken = $auth::getFromToken($token);

        if(!isset($token)){
            throw new \BadMethodCallException("Missing parameters", HttpStatusCode::BAD_REQUEST);
        }

        $userInDb = $this->db->procedure("GetUserByToken", [
            "token" => $token
        ]);
        
        if( count($userInDb) > 0 && $userInDb[0]['id'] === $userInToken['id']) {
            $auth::createAccessToken($user);  //sets header, no need to returns it
        }

    }
}