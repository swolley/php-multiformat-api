<?php

// /use Api\Core;

if(!file_exists("./config.php")){
    exit("No config file found. Rename config.php.example into config.php and fill db infos");
}

require_once('./config.php');
require_once("./vendor/autoload.php");
require_once('./src/core/bootstrap.php');

new Api\Core\Router;
