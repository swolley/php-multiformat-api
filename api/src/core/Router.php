<?php
namespace Api\Core;
//use Api\Routes;
use Api\Core\HttpStatusCode;
use Api\Local\Auth;
//use \ReflectionClass;

final class Router{
    private static $instance;
    private $request;
    private $response;

    /**
     * singleton handler returns instance of Router if already exists else create new one
     * @return  Router                  Router instance
     */
    public static function getInstance() : Router{
        if ( !isset(self::$instance) ) {
            $class = __CLASS__;
            self::$instance = new $class;
        }

        return self::$instance;
    }

    private function __construct(/*$method, $request, $token, $format = 'application/json', $parameters = []*/){
    }

    /**
     * main request's handler called every time a request is catched
     */
    public function handleRequest() {
        try {
            //create new request and response every time?
            $this->response = new Response();
            $this->request = new Request();

            //check user data and permission level
            if( $this->needsAuthentication() ){
                $authorized = Auth::authorizeRequest($this->request);
                if( !$authorized ){
                    throw new \Exception('User not authorized', HttpStatusCode::FORBIDDEN);
                }
            }

            //check request and action
            if( $this->request->getResource() === null ){
                throw new \Exception('No resource specified', HttpStatusCode::NOT_FOUND);
            }

            $controller_full_name = 'Api\\Routes\\'.ucfirst($this->request->getResource());
            $controller = new $controller_full_name;
            $method = $this->request->getMethod();

            if( !method_exists($controller, $method) ){
                throw new \Exception('No action found', HttpStatusCode::NOT_FOUND);
            }
            
            //makes request and returns data
            $this->response->setContent($controller->$method($this->request));

            unset($controller_full_name);
            unset($controller);
            unset($method);

            //make response
            if($this->response->hasContent()){
                exit( $this->response->ok($this->request));
            } else {
                throw new \Exception("Internal Server Error", HttpStatusCode::INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $ex) {
            exit($this->response->error($this->request, $ex));
        }
    }
    
    /**
     * check from excluded requests in main config to understand if request neeeds to be authenticated 
     */
    private function needsAuthentication() : bool {
        global $exclude_from_auth;    //declared in config.php file
        $resource = $this->request->getResource();
        return !(in_array($resource, array_keys($exclude_from_auth)) && in_array($this->request->getMethod(), array_keys($exclude_from_auth[$resource])));
    }
}