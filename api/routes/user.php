<?php

class User extends Auth {
    public function post($userData){
        //check if user exists
        if(strlen($userData['email']) === 0 || strlen($userData['password']) === 0){
            return "no parameters";
        }

        $user = $this->db->procedure("GetUserByCredentials", [
            "email" => $userData['email'],
            "hashedPassword" => hash("sha256", $userData['password'])
        ]);

        //temp perchè non funziona la query
        $user = [['id'=>1]];

        if(count($user) === 0){
            return "No user found";
        }

        $token = static::createToken($user[0]['id']);

        $result = $this->db->procedure("InsertUserToken", [
            "userId" => $user[0]['id'],
            "token" => $token
        ]);

        return [
            'data' => $result['token']
        ]; 
    }

    public function delete($userData){
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