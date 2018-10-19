<?php
namespace Api\Routes;
use Api\Core\RouteModel;
use Api\Core\Request;
use Api\Core\Response;
use Api\Core\HttpStatusCode;

final class Frontend extends RouteModel {
    public function  __construct( ) {
        parent::__construct();
    }

    public function get(Request &$request, Response &$response) {
        $response->prepare([
            'data' => array_values(array_map(function($file) {
                $route = strtolower(strstr($file, ".php", TRUE));
                return [
                    "name" => "",
                    "route" => $route,
                    "uri" => $_SERVER['SERVER_NAME'] . '/api/' . $route
                ];
            }, array_diff(static::getRoutes(), ['Frontend.php'])))
        ]);
    }

    public function post(Request &$request, Response &$response) {
        throw new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);
    }

    public function delete(Request &$request, Response &$response) {
        throw new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);
    }

    public function put(Request &$request, Response &$response) {
        throw new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);
    }

    public function patch(Request &$request, Response &$response) {
        throw new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);
    }
}