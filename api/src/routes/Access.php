<?php
namespace Api\Routes;
use Api\Core\RouteModel;
use Api\Core\Request;
use Api\Core\HttpStatusCode;
use Api\Local\Auth;

class Access extends RouteModel {

    public function  __construct(){
        parent::__construct();
    }

    public function put(Request &$request) {
        throw new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);
    } 

    public function get(Request &$request) {
        throw new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);
    }

    public function post(Request &$request) {
        $params = $request->getParameters();
        
        //check if user exists
        if(!isset($params['email'], $params['password'])){
            throw new \BadMethodCallException('Missing parameters', HttpStatusCode::BAD_REQUEST);
        }

        if(strlen($params['email']) === 0 || !filter_var($params['email'], FILTER_VALIDATE_EMAIL) || strlen($params['password']) === 0){
            throw new \InvalidArgumentException('Invalid parameters vaules', HttpStatusCode::BAD_REQUEST);
        }

        $user = $this->db->procedure('GetUserByCredentials', [
            'email' => $params['email'],
            'hashedPassword' => hash('sha256', $params['password'])
        ]);

        if(count($user) === 0){
            return [];
        }

        $user = $user[0];
        $user['permissions'] = json_decode($user['permissions']);

        $auth = new Auth();
        $auth->createAccessToken($user);  //sets header, no need to returns it
        $refresh_token = $auth->createRefreshToken($user);

        $this->db->procedure('InsertUserToken', [
            'userId' => $user['id'],
            'token' => $refresh_token
        ]);

        return [
            'data' => $user
        ]; 
    }

    public function delete(Request &$request) {
        $params = $request->getParameters();
        $token = $request->getToken();
        
        if( !isset($params['id'], $token) ){
            throw new \BadMethodCallException('Missing parameters', HttpStatusCode::BAD_REQUEST);
        }

        $user = Auth::getFromToken($token);

        if($user['id'] == $params['id']){
            $result = $this->db->procedure('DeleteUserTokens', [
                'token' => $token
            ]);
            
            if($result != 0) {
                return [
                    'data' => 'logged out'
                ];
            }
        }
            
        throw new \Exception('Not authorized', HttpStatusCode::UNAUTHORIZED);
    }

    public function patch(Request &$request) {
        $params = $request->getParameters();
        $token = $request->getToken();
        
        if(!isset($params['id'], $token)){
            throw new \BadMethodCallException('Missing parameters', HttpStatusCode::BAD_REQUEST);
        }
        
        $auth = new Auth();
        $user_in_token = $auth::getFromToken($token);
        
        if( $user_in_token['id'] === $params['id'] ){
            $user_in_db = $this->db->procedure('GetUserByToken', [
                'token' => $token
            ]);
            
            if( count($user_in_db) > 0 && $user_in_db[0]['id'] === $user_in_token['id'] ) {
                $auth->createAccessToken($user);  //sets header, no need to returns it
                return [
                    'data' => 'Acces token refreshed'
                ];
            }
        }

        throw new \Exception('Not authorized', HttpStatusCode::UNAUTHORIZED);
    }
}