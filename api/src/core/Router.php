<?php
namespace Api\Core;
//use Api\Routes;
use Api\Core\HttpStatusCode;
use Api\Local\Auth;
use \ReflectionClass;

class Router{
    public function __construct(/*$method, $request, $token, $format = 'application/json', $parameters = []*/){
        $this->handleRoute($this->getHeader());
    }

    private function getHeader() : array {
        $headers = apache_request_headers();
        $requestUri = explode('/', $_SERVER['REQUEST_URI']);
        return [
            'method' => strtolower($_SERVER['REQUEST_METHOD']), //http verb
            'request' => end($requestUri),  //resource path
            'token' => isset($headers['Authentication']) ? 
                trim(str_replace('Bearer', '', $headers['Authentication'])) : 
                null,   //auth token
            'format' => isset($headers['Accept']) ? $headers['Accept'] : null,  //result format (json, html)
            'filters' => $headers,  //remaining headers elements are filters
            'parameters' => json_decode(file_get_contents('php://input'), true) //body content
        ];
    }

    private function handleRoute(array $requestArray): void {
        extract($requestArray); //extracts method's variables
        //check user data and permission level
        if($this->needsAuthentication($request, $method)){
            if(!(new Auth())->authorizeRequest(['token' => $token, 'request' => $request, 'method' => $method])){
                Response::error('user not authorized', HttpStatusCode::FORBIDDEN);
            }
        }

        //check request and action
        if(!isset($request)){
            Response::error('No resource specified', HttpStatusCode::BAD_REQUEST);
        }
        $controllerFullName = 'Api\\Routes\\'.ucfirst($request);
        $controller = new $controllerFullName;

        if(!method_exists($controller, $method)){
            Response::error('No action found', HttpStatusCode::NOT_FOUND);
        }
        
        //make request and return data
        $result = $controller->$method($parameters);

        //make response
        return exit(isset($result['data'])
            ? (Response::ok($result, $format, "${method}_${request}"))
            : Response::error($result)); 
    }

    private function needsAuthentication(string $request, string $method) : bool {
        global $excludeFromAuth;    //declared in config.php file

        return !(in_array($request, array_keys($excludeFromAuth)) && in_array($method, array_keys($excludeFromAuth[$request])));
    }
}