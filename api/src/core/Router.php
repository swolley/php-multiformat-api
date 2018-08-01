<?php
namespace Api\Core;
//use Api\Routes;
use Api\Core\HttpStatusCode;
use Api\Local\Auth;
use \ReflectionClass;

final class Router{
    private static $instance;
    private $request;
    private $response;

    public static function getInstance(){
        if (!isset(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class;
        }

        return self::$instance;
    }

    private function __construct(/*$method, $request, $token, $format = 'application/json', $parameters = []*/){
    }

    private function extractElements(array &$list, array $elementKey) {
        $values = [];
        foreach($elementKey as $key){
            if(isset($list[$key])){
                array_push($values, $list[$key]);
                unset($list[$key]);
            }
        }

        return $values !== []
            ? (count($values) === 1
                ? $values[0]
                : $values)
            : NULL;
    }

    private function groupParameters($pathId = null) : array {
        $bodyParameters = json_decode(file_get_contents('php://input'), TRUE) ?:[]; //body content
        
        if(!is_null($pathId)){
            $bodyParameters['id'] = $pathId;
        }
        
        return array_merge($_GET, $bodyParameters);
    }

    private function parseRequest() {
        $headers = apache_request_headers();
        $requestUri = array_diff(explode('/', explode('?', str_replace(API_URL, "", $_SERVER['REQUEST_URI']), 2)[0]), ["", "api"]);

        $this->request = [
            'method' => strtolower($_SERVER['REQUEST_METHOD']), //http verb
            'resource' => $this->extractElements($requestUri, [array_keys($requestUri)[0]]),  //resource path
            'token' => $this->extractElements($headers, ['Authorization']),   //auth token
            'responseFormat' => $this->extractElements($headers, ['Accept']),    //result format (json, html)
            'filters' => $this->extractElements($headers, ['X-Result', 'X-Total', 'X-From']),  //filters for pagination,
            'parameters' => $this->groupParameters(!empty($requestUri) ? end($requestUri) : null),   //joined get, body parameters and optional id at the end of uri
            'others' => $headers    //remaining header's elements
        ];
    }

    public function handleRequest() {
        $this->parseRequest();
        //extract($this->request, EXTR_REFS); //extracts method's variables
        //check user data and permission level
        if($this->needsAuthentication()){
            if(!(new Auth())->authorizeRequest([
                    'token' => $this->request['token'], 
                    'resource' => $this->request['resource'], 
                    'method' => $this->request['method']
                ])){
                Response::error('user not authorized', HttpStatusCode::FORBIDDEN);
            }
        }

        //check request and action
        if(!isset($this->request['resource'])){
            Response::error('No resource specified', HttpStatusCode::BAD_REQUEST);
        }
        $controllerFullName = 'Api\\Routes\\'.ucfirst($this->request['resource']);
        $controller = new $controllerFullName;
        $method = $this->request['method'];

        if(!method_exists($controller, $method)){
            Response::error('No action found', HttpStatusCode::NOT_FOUND);
        }
        
        //make request and return data
        $this->response = $controller->$method($this->request['parameters']);

        unset($controllerFullName);
        unset($controller);
        //make response
    }
    
    public function send(){
        exit(isset($this->response['data'])
            ? (Response::ok($this->response, $this->request))
            : Response::error($this->response)); 
    }

    private function needsAuthentication() : bool {
        global $excludeFromAuth;    //declared in config.php file

        return !(in_array($this->request['resource'], array_keys($excludeFromAuth)) && in_array($this->request['method'], array_keys($excludeFromAuth[$this->request['resource']])));
    }
}