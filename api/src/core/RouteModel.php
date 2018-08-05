<?php
namespace Api\Core;
use Api\Core\Request;

abstract class RouteModel implements ICrudable {
    protected $db;

    /**
     * assign to $db a new instance of Database class
     */
    public function __construct(){
        $this->db = new Database();
    }

    public abstract function get(Request &$request);
    public abstract function post(Request &$request);
    public abstract function delete(Request &$request);
    public abstract function put(Request &$request);
    public abstract function patch(Request &$request);
}