<?php
namespace Api\Routes;
use Api\Core\RouteModel;
use Api\Core\Request;
use Api\Core\HttpStatusCode;

class Frontend extends RouteModel {
    public function  __construct(){
        parent::__construct();
    }

    public function get(Request &$request) {
        return [
            'data' => array_values(array_map(function($file){
                $route = strtolower(strstr($file, ".php", TRUE));
                return [
                    "name" => "",
                    "route" => $route,
                    "uri" => $_SERVER['SERVER_NAME'] . '/api/' . $route
                ];
            }, array_diff(scandir(ROUTES), ['.', '..', 'Frontend.php'])))
        ];
    }

    public function post(Request &$request) {
        throw new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);
    }

    public function delete(Request &$request) {
        throw new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);
    }

    public function put(Request &$request) {
        throw new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);
    }

    public function patch(Request &$request) {
        throw new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);
    }
}