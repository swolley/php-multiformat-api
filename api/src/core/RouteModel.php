<?php
namespace Api\Core;
use Api\Core\Request;
use Api\Core\Response;

abstract class RouteModel implements ICrudable {
    protected $db;

    /**
     * assign to $db a new instance of Database class
     */
    public function __construct( ) {
        $this->db = new Database();
    }

    public static function getRoutes() {
        return array_diff(scandir(ROUTES), ['.', '..']);
    }

    public abstract function get(Request &$request, Response &$response);
    public abstract function post(Request &$request, Response &$response);
    public abstract function delete(Request &$request, Response &$response);
    public abstract function put(Request &$request, Response &$response);
    public abstract function patch(Request &$request, Response &$response);
}