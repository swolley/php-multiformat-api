<?php
namespace Api\Routes;
use Api\Core\RouteModel;

class Frontend extends RouteModel {
    public function  __construct(){
        parent::__construct();
    }

    public function get($params=[]) {
        return [
            'data' => array_reduce(function($file){
                return strtolower(strstr($file, ".php", TRUE));
            }, array_diff(scandir(ROUTES), ['.', '..', 'Frontend']))
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