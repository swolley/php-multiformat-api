<?php

class User extends Auth {
    public function post($userData){
        //check if user exists
        if(!isset($userData['email'], $userData['password'])){
            return "no parameters";
        }

        $user = $this->db->select("SELECT id FROM users WHERE email=:email AND password=:password", [
            "email" => $userData['email'],
            "password" => hash("sha256", $userData['password'])
        ]);

        if(count($user) === 0){
            return "No user found";
        }

        $token = $this->createToken($user['id']);

        return [
            'data' => $token
        ]; 
    }
}