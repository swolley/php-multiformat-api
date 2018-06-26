<?php
function __autoload($class){
    $path = CORE . strtolower($class) . '.php';
    if(!(include_once($path)) || !(class_exists($class))){
        $path = ROUTES . strtolower($class) . '.php';
        if(!(include_once($path)) || !(class_exists($class))){
            Response::error("No function found", 404);
        }
    }

}