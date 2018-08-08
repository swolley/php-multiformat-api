<?php
namespace Api\Local;
use Api\Core\AuthModel;
use Api\Core\HttpStatusCode;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\BeforeValidException;

final class Auth extends AuthModel {

    public function __construct( ) {
        parent::__construct();
    }

    public static function getFromToken(string $token) : array {
        try{
            $auth_data = explode(" ", $token);
        
            //authorizes only configured auth type
            if( $auth_data[0] !== ucfirst(AUTH_METHOD) ) {
                throw new \InvalidArgumentException('Token type not handled', HttpStatusCode::BAD_REQUEST);
            }

            $secret = base64_decode(KEY);
            $user = (array)JWT::decode($auth_data[1], $secret, array('HS256'));
            return $user['data'];
        } catch(ExpiredException $ex) {
            throw new ExpiredException('Token expired', HttpStatusCode::UNAUTHORIZED);
        } catch (BeforeValidException $ex) {
            throw new BeforeValidException($ex->getMessage(), HttpStatusCode::UNAUTHORIZED);
        } catch(Exception $ex) {
            throw new \BadMethodCallException('Missing parameters', HttpStatusCode::BAD_REQUEST);
        }
    }

    public function createToken(array &$user, int $lifeTime = 0) : string {
        $iat = time();
        $secret = base64_decode(KEY);
        $data = [
            'iss' => SERVERNAME,
            'iat' => $iat,
            'nbf' => $lifeTime !== 0 ? $iat : $iat + ACCESS_T_EXPIRES,  //if refresh token no needs to uses it before access expires. if access lost, do login
            'jti' => base64_encode(mcrypt_create_iv(32)),
            'data' => [
                $user['id'],
                $user['name'],
                $user['permissions']
            ]
        ];

        if( $lifeTime !== 0 ) {
            //is access token needs expire date
            $data['exp'] = $iat + $lifeTime;
        }

        return JWT::encode($data, $secret, 'HS256');
    }

    public function createAccessToken(array &$user) {
        $accessToken = $this->createToken($user, ACCESS_T_EXPIRES);
        header("Access-Token: Bearer {$accessToken}");
    }
    
    public function createRefreshToken(array &$user) : string {
        $refreshToken = $this->createToken($user);
        header("Refresh-Token: Bearer {$refreshToken}");
        return $refreshToken;   //returns it because db persist needed
    }

}