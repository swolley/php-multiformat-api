<?php

if(!file_exists('./config.php')){
    exit('No config file found.' /*Rename config.php.example into config.php and fill db infos'*/);
}

//load config
require_once('./config.php');
//load composer's autoloader
require_once('./vendor/autoload.php');
//do bootstrap functions if defined
require_once('./src/local/bootstrap.php');

//launch main Router
new Api\Core\Router;
