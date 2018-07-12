<?php
namespace Routes;

class User implements ICrud {
    use Auth;

    public function update($params=[]){
        throw new NotImplementedException();
    }
    public function put($params=[]){
        throw new NotImplementedException();
    } 

    public function get($params=[]){
        return [
            'rowId' => 'id',
            'data' => isset($params['id']) ? 
                $this->db->procedure("GetUser", ['id' => $params['id']]) : 
                $this->db->procedure("GetAllUSers")
        ];
    }

    public function post($params=[]){
        //check if user exists
        if(strlen($params['email']) === 0 || strlen($params['password']) === 0){
            return "no parameters";
        }

        $user = $this->db->procedure("GetUserByCredentials", [
            "email" => $params['email'],
            "hashedPassword" => hash("sha256", $params['password'])
        ]);

        //temp perchè non funziona la query
        //$user = [['id'=>1]];

        if(count($user) === 0){
            return "No user found";
        }

        $token = static::createToken($user[0]['id']);

        $result = $this->db->procedure("InsertUserToken", [
            "userId" => $user[0]['id'],
            "token" => $token
        ]);

        return [
            'data' => $result[0]['token']
        ]; 
    }

    public function delete($params=[]){
        if(!isset($userData['token'])){
            return "No token found";
        }

        $result = $this->db->delete("user_tokens", "token=:token", [
            "token" => $userData['token']
        ]);

        return $result != 0 ? [
            "data" => "logged out"
        ] : "No token found";
    }
}