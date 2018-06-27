<?php
//namespace Libs;

class Router{
    public function __construct(/*$method, $request, $token, $format = 'application/json', $parameters = []*/){
        $this->getHeaders();
    }

    private function getHeaders(){
        $headers = apache_request_headers();
        $token = isset($headers['x-auth-token']) ? $headers['x-auth-token'] : null;
        $format = isset($headers['accept']) ? $headers['accept'] : null;
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $request = explode("/", $_SERVER['REQUEST_URI']);
        $request = end($request);
        $parameters = json_decode(file_get_contents('php://input'), true);
        $this->handleRoute($method, $request, $token, $format, $parameters);
    }

    private function handleRoute($method, $request, $token, $format, $parameters){
        //check user data and permission level
        if($request !== "user" && $method !== "post" ){
            if(!((new User())->authorizeRequest(['token' => $token, 'request' => $request, 'method' => $method]))){
                Response::error("user not authorized", 401);
            }
        }

        //check request and action
        if(!isset($request)){
            Response::error("function not found", 404);
        }

        $controller = new $request();
        if(!method_exists($controller, $method)){
            Response::error("function not found", 404);
        }
        
        //make request and return data
        $result = $controller->$method($parameters);

        //make response

        return isset($result['data']) ? 
            ($format === 'text/html' ? Response::render($result['data'], "${method}_${request}") : Response::send($result)) : 
            Response::error($result); 
    }
}