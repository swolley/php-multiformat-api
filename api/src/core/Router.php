<?php
namespace Api\Core;
//use Api\Routes;
use Api\Core\HttpStatusCode;
use Api\Local\Auth;
use \ReflectionClass;

class Router{
    public function __construct(/*$method, $request, $token, $format = 'application/json', $parameters = []*/){
        $this->handleRequest($this->parseRequest());
    }

    private function extractHeaderElements(array &$headers, array $elementKey) {
        $values = [];
        foreach($elementKey as $key){
            if(isset($headers[$key])){
                array_push($values, $headers[$key]);
                unset($headers[$key]);
            }
        }

        return $values !== []
            ? (count($values) === 1
                ? $values[0]
                : $values)
            : NULL;
    }

    private function groupParameters() : array {
        $bodyParameters = json_decode(file_get_contents('php://input'), TRUE); //body content
        return $bodyParameters !== NULL 
            ? array_merge($_GET, $bodyParameters)
            : $_GET;
    }

    private function parseRequest() : array {
        $headers = apache_request_headers();
        $requestUri = array_diff(explode('/', $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0]), [""]);

        return [
            'method' => strtolower($_SERVER['REQUEST_METHOD']), //http verb
            'request' => end($requestUri),  //resource path //TODO: to be fixed
            'token' => $this->extractHeaderElements($headers, ['Authorization']),   //auth token
            'format' => $this->extractHeaderElements($headers, ['Accept']),    //result format (json, html)
            'filters' => $this->extractHeaderElements($headers, ['X-Result', 'X-Total', 'X-From']),  //filters for pagination,
            'parameters' => $this->groupParameters(),   //joined get and body parameters
            'others' => $headers    //remaining header's elements
        ];
    }

    private function handleRequest(array $requestArray): void {
        extract($requestArray, EXTR_REFS); //extracts method's variables
        //check user data and permission level
        if($this->needsAuthentication($request, $method)){
            if(!(new Auth())->authorizeRequest([
                    'token' => $token, 
                    'request' => $request, 
                    'method' => $method
                ])){
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