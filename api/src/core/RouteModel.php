<?php
namespace Api\Core;

abstract class RouteModel implements ICrudable {
    protected $db;

    /**
     * assign to $db a new instance of Database class
     */
    public function __construct(){
        $this->db = new Database();
    }

    public abstract function get($params=[]);
    public abstract function post($params=[]);
    public abstract function delete($params=[]);
    public abstract function update($params=[]);
    public abstract function put($params=[]);
    public abstract function patch($params=[]);
}