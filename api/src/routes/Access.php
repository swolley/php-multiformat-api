<?php
namespace Api\Routes;
use Api\Core\RouteModel;
use Api\Core\Request;
use Api\Core\Response;
use Api\Core\HttpStatusCode;
use Api\Local\Auth;

final class Access extends RouteModel {

    public function  __construct( ) {
        parent::__construct();
    }

    public function put(Request &$request, Response &$response) {
        throw new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);
    } 

    public function get(Request &$request, Response &$response) {
        throw new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);
    }

	// login
    public function post(Request &$request, Response &$response) {
        $params = $request->getParameters();
        
        //check if user exists
        if( !isset($params['email'], $params['password']) ) {
            $response->error('Missing parameters', HttpStatusCode::BAD_REQUEST);
        }

        if( strlen($params['email']) === 0 || !filter_var($params['email'], FILTER_VALIDATE_EMAIL) || strlen($params['password']) === 0 ) {
            $response->error('Invalid parameters values', HttpStatusCode::BAD_REQUEST);
        }

        $user = $this->db->procedure('GetUserByCredentials', [
            'email' => $params['email'],
            'hashedPassword' => hash('sha256', $params['password'])
        ]);

        if( count($user) === 0 ) {
            $response->error('User not found', HttpStatusCode::UNAUTHORIZED);
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

        $response->prepare([
            'data' => $user
        ], HttpStatusCode::CREATED); 
    }

	//logout
    public function delete(Request &$request, Response &$response) {
        $params = $request->getParameters();
        $token = $request->getToken();
        
        if( !isset($params['id'], $token) ) {
           $response->error('Missing parameters', HttpStatusCode::BAD_REQUEST);
        }

        $user = Auth::getFromToken($token);

        if( $user['id'] !== $params['id'] ) {
            $response->error('Not authorized', HttpStatusCode::UNAUTHORIZED);
        }

        $result = $this->db->procedure('DeleteUserTokens', [
            'token' => $token
        ]);
        
        $result != 0
            ? $response->prepare(['data' => 'logged out'])
            : $response->error();
    }

	// refresh token
    public function patch(Request &$request, Response &$response) {
        $params = $request->getParameters();
        $token = $request->getToken();
        
        if( !isset($params['id'], $token) ) {
            $response->error('Missing parameters', HttpStatusCode::BAD_REQUEST);
        }

        $auth = new Auth();
        $user_in_token = $auth::getFromToken($token);
        
        if( $user_in_token[0] !== $params['id'] ) {
            $response->error('Not authorized', HttpStatusCode::UNAUTHORIZED);
        }

        $user_in_db = $this->db->procedure('GetUserByToken', [
            'token' => $token
        ]);
        
        if( count($user_in_db) > 0 && $user_in_db[0]['id'] === $user_in_token[0] ) {
            $auth->createAccessToken($user);  //sets header, no need to returns it
            $response->prepare([
                'data' => 'Acces token refreshed'
            ]);
        } else {
            $response->error('No token found, please re-login', HttpStatusCode::UNAUTHORIZED);
        }
    }
}