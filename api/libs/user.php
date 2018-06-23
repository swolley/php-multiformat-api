<?php

class User {
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function authorizeRequest($userData){
        return isset($userData['token'], $userData['request'], $userData['method']) ? $this->canDoRequest($userData) : false;
    }

    private function canDoRequest($userData){
        $user = $this->getFromToken($userData['token']);
        if(count($user) > 0){
            $permission = $userData['method']."_".$userData['request'];
            return in_array($permission, json_decode($user[0]['permissions']));
        }
    }

    private function getFromToken($token){
        return $this->db->select("SELECT u.* FROM users u INNER JOIN user_tokens t ON u.id=t.userId WHERE t.token=:token", [
            "token" => $token
        ]);
    }
}