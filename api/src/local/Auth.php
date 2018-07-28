<?php
namespace Api\Local;
use Api\Core\AuthModel;
use Firebase\JWT\JWT;

class Auth extends AuthModel {
    //private $db;

    public function __construct(){
        parent::__construct();
    }

    protected function getFromToken(string &$jwt) : array {
        /*return $this->db->procedure("GetUserByToken", [
            "token" => $token
        ]);*/
        $secret = base64_decode(KEY);
        $user = (array)JWT::decode($jwt, $secret, array('HS256'));
        return $user['data'];
    }

    public static function createToken(array &$user) : string {
        $iat = time();
        $secret = base64_decode(KEY);
        $data = [
            'iss' => SERVERNAME,
            'iat' => $iat,
            'nbf' => $iat,
            'jti' => base64_encode(mcrypt_create_iv(32)),
            'data' => [
                $user['id'],
                $user['name'],
                $user['permissions']
            ]
        ];

        return JWT::encode($data, $secret, 'HS256');
    }
}