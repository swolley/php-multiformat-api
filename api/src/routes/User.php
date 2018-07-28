<?php
namespace Api\Routes;
use Api\Core\RouteModel;
use Api\Local\Auth;

class User extends RouteModel {

    public function  __construct(){
        parent::__construct();
    }

    public function update($params=[]) {
        throw new NotImplementedException();
    }
    public function put($params=[]) {
        throw new NotImplementedException();
    } 

    public function get($params=[]) {
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

    public function post($params=[]) {
        //check if user exists
        if(strlen($params['email']) === 0 || !filter_var($params['email'], FILTER_VALIDATE_EMAIL) || strlen($params['password']) === 0){
            return 'no parameters';
        }

        $user = $this->db->procedure('GetUserByCredentials', [
            'email' => $params['email'],
            'hashedPassword' => hash('sha256', $params['password'])
        ]);

        if(count($user) === 0){
            return [];
        }

        $token = (new Auth())->createToken($user[0]);

        /*$result = $this->db->procedure("InsertUserToken", [
            "userId" => $user[0]['id'],
            "token" => $token
        ]);*/

        return [
            'data' => $token
        ]; 
    }

    public function delete($params=[]) {
        if(!isset($userData['token'])){
            return 'No token found';
        }

        $result = $this->db->delete('user_tokens', 'token=:token', [
            'token' => $userData['token']
        ]);

        return $result != 0 ? [
            'data' => 'logged out'
        ] : 'No token found';
    }

    public function patch($params=[]) {
        throw new NotImplementedException();
    }
}