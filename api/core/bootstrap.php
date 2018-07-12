<?php
use Core;
use Routes;

//add here everything needed before startup
//sqitched to composer autoloader

/*function __autoload($class){
    $paths = [CORE, ROUTES];
    foreach ($paths as $directory) {
        if(file_exists($directory . strtolower($class) . '.php')){
            include_once($directory . strtolower($class) . '.php');
            if(!class_exists($class) && !trait_exists($class)){
                Response::error("No function found", 404);
            }
            
            break;
        }
    }
}*/