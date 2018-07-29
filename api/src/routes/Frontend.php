<?php
namespace Api\Routes;
use Api\Core\RouteModel;

class Frontend extends RouteModel {
    public function  __construct(){
        parent::__construct();
    }

    public function get($params=[]) {
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

    public function post($params=[]) {
        throw new NotImplementedException();
    }

    public function delete($params=[]) {
        throw new NotImplementedException();
    }

    public function update($params=[]) {
        throw new NotImplementedException();
    }

    public function put($params=[]) {
        throw new NotImplementedException();
    }

    public function patch($params=[]) {
        throw new NotImplementedException();
    }
}