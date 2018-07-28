<?php
namespace Api\Routes;
use Api\Core\RouteModel;
use Api\Local\Auth;

class User extends RouteModel {

    public function  __construct(){
        parent::__construct();
    }

    public function update($params=[]) : array {
        throw new NotImplementedException();
    }
    public function put($params=[]) : array {
        throw new NotImplementedException();
    } 

    public function get($params=[]) : array {
        return [
            'rowId' => 'id',    //TODO: don't like so much this solution to responde primary key
            'data' => isset($params['id']) ? 
                $this->db->procedure('GetUser', ['id' => $params['id']]) : 
                $this->db->procedure('GetAllUsers')
        ];
    }

    public function post($params=[]) {
        //check if user exists
        if(strlen($params['email']) === 0 || strlen($params['password']) === 0){
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

    public function delete($params=[]) : array {
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

    public function patch($params=[]) : array {
        throw new NotImplementedException();
    }
}