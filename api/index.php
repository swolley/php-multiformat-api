<?php

require_once('./config.php');

function __autoload($class){
    $path = LIBS. strtolower($class) . '.php';
    if(!(include_once($path)) || !(class_exists($class))){
        Response::error("No function found", 404);
    }

}

$headers = apache_request_headers();
$token = isset($headers['x-auth-token']) ? $headers['x-auth-token'] : null;
$result = isset($headers['accept']) ? $headers['accept'] : null;
$method = strtolower($_SERVER['REQUEST_METHOD']);
$request = explode("/", $_SERVER['REQUEST_URI']);
$request = end($request);
$parameters = json_decode(file_get_contents('php://input'), true);

new Router($method, $request, $token, $result, $parameters);