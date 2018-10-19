<?php
namespace Api\Core;
use Api\Core\HttpStatusCode;
use Api\Local\Auth;

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

    private function __construct( ) {
    }

    /**
     * main request's handler called every time a request is catched
     */
    public function handleRequest() {
        try {
            //create new request and response every time?
            $this->response = new Response();
            $this->request = new Request($this->response);

			$resource = $this->request->getResource();
            //check user data and permission level
            if(  $this->needsAuthentication($resource) && !Auth::authorizeRequest($this->request) ) {
                //if(  !Auth::authorizeRequest($this->request)  ) {
                    $this->response->error('User not authorized', HttpStatusCode::FORBIDDEN);
                //}
            }

            //check request and action
            if(  $resource === null  ) {
                $this->response->error('No resource specified', HttpStatusCode::NOT_FOUND);
            }

            $controller_full_name = 'Api\\Routes\\'.ucfirst($resource);
            $controller = new $controller_full_name;
            $method = $this->request->getMethod();

            if(  !method_exists($controller, $method)  ) {
                $this->response->error('No action found', HttpStatusCode::NOT_FOUND);
            }
            
            //makes request and returns data
            $controller->$method($this->request, $this->response);
        } catch (\Exception $ex) {
            $this->response->prepare($ex->getMessage(), $ex->getCode());
        } finally {
            exit($this->response->send());
        }
    }
    
    /**
     * check from excluded requests in main config to understand if request neeeds to be authenticated 
	 * @param	string	$resource	resource name
     */
    private function needsAuthentication(&$resource) : bool {
        global $exclude_from_auth;    //declared in config.php file
        //$resource = $this->request->getResource();
        return !(in_array($resource, array_keys($exclude_from_auth)) && in_array($this->request->getMethod(), array_keys($exclude_from_auth[$resource])));
    }
}