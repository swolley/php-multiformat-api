<?php
//namespace Libs;

class Router{
    public function __construct($method, $request, $token, $format = 'application/json', $parameters = []){
        $this->handleRoute($method, $request, $token, $format, $parameters);
    }

    private function handleRoute($method, $request, $token, $format, $parameters){
        //check user data and permission level
        if(!((new User())->authorizeRequest(['token' => $token, 'request' => $request, 'method' => $method]))){
            Response::error("user not authorized", 401);
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