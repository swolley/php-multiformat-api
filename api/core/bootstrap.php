<?php

function __autoload($class){
    $paths = [CORE, ROUTES];
    foreach ($paths as $directory) {
        if(file_exists($directory . strtolower($class) . '.php')){
            include_once($directory . strtolower($class) . '.php');
            if(!class_exists($class)){
                Response::error("No function found", 404);
            }
            
            break;
        }
    }
}