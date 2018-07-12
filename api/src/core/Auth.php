<?php
namespace Api\Core;
use Firebase\JWT\JWT;

class Auth {
    //private $db;

    /* public function __construct(){
        $this->db = new Database();
    } */

    public function authorizeRequest(array $request) : bool {
        return isset($request['token'], $request['request'], $request['method']) ? $this->canDoRequest($request) : false;
    }

    protected function canDoRequest(array $request) : bool {
        $user = $this->getFromToken($request['token']);
        if(isset($user)){
            $permission = $request['method']."_".$request['request'];
            return in_array($permission, json_decode($user[2]));
        }

        return false;
    }

    protected function getFromToken(string $jwt) : array {
        /*return $this->db->procedure("GetUserByToken", [
            "token" => $token
        ]);*/
        $secret = base64_decode(KEY);
        $user = (array)JWT::decode($jwt, $secret, array('HS256'));
        return $user["data"];
    }

    public static function createToken(array $user) : string {
        $iat = time();
        $secret = base64_decode(KEY);
        $data = [
            "iss" => SERVERNAME,
            "iat" => $iat,
            "nbf" => $iat,
            "jti" => base64_encode(mcrypt_create_iv(32)),
            "data" => [
                $user['id'],
                $user['name'],
                $user['permissions']
            ]
        ];

        return JWT::encode($data, $secret, "HS256");
    }
}