<?php
namespace Api\Core;
//use Api\Routes;
use Api\Core\HttpStatusCode;
use Api\Core\Auth;
use \ReflectionClass;

class Router{
    public function __construct(/*$method, $request, $token, $format = 'application/json', $parameters = []*/){
        $request = $this->getHeader();
        $this->handleRoute($request);
    }

    private function getHeader() : array {
        $headers = apache_request_headers();
        $requestUri = explode("/", $_SERVER['REQUEST_URI']);
        return [
            "method" => strtolower($_SERVER['REQUEST_METHOD']),
            "request" => end($requestUri),
            "token" => isset($headers['x-auth-token']) ? $headers['x-auth-token'] : null,
            "format" => isset($headers['accept']) ? $headers['accept'] : null,
            "parameters" => json_decode(file_get_contents('php://input'), true)
        ];
    }

    private function handleRoute(array $requestArray): void {
        extract($requestArray);
        //check user data and permission level
        if($request !== "user" || $method !== "post" ){
            if(!((new Auth())->authorizeRequest(['token' => $token, 'request' => $request, 'method' => $method]))){
                Response::error("user not authorized", HttpStatusCode::UNAUTHORIZED);
                
            }
        }

        //check request and action
        if(!isset($request)){
            Response::error("No resource specified", HttpStatusCode::BAD_REQUEST);
        }
        $controllerFullName = "Api\\Routes\\".ucfirst($request);
        $controller = new $controllerFullName;

        if(!method_exists($controller, $method)){
            Response::error("No action found", HttpStatusCode::NOT_FOUND);
        }
        
        //make request and return data
        $result = $controller->$method($parameters);

        //make response
        return isset($result['data']) ? 
            (
                $format === 'text/html' ? 
                    Response::render($result['data'], "${method}_${request}") : 
                    Response::send($result)
            ) : 
            Response::error($result); 
    }
}