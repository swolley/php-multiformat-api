<?php
namespace Api\Routes;
use Api\Core\RouteModel;

class Frontend extends RouteModel {
    public function  __construct(){
        parent::__construct();
    }

    public function get($params=[]) : array {
        throw new NotImplementedException();
    }

    public function post($params=[]) {
        throw new NotImplementedException();
    }

    public function delete($params=[]) : array {
        throw new NotImplementedException();
    }

    public function update($params=[]) : array {
        throw new NotImplementedException();
    }

    public function put($params=[]) : array {
        throw new NotImplementedException();
    }
}