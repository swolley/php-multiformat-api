<?php
//errors and debug
define('DEBUG_MODE', FALSE);                         //set true during debug else false

error_reporting(DEBUG_MODE ? E_ALL : NULL);
ini_set('display_errors', DEBUG_MODE ? 1 : 0);

//Database
define('DB_TYPE', 'dbtype');                        //change here
define('DB_HOST', 'host');                          //change here
define('DB_NAME', 'dbname');                        //change here
define('DB_USER', 'dbuser');                        //change here
define('DB_PASS', 'dbpassword');                    //change here
define('DB_CHARSET', 'utf8');                       //change here

//Paths
define('API_URL', 'phpApi/api');
define('CORE', __DIR__ . '/src/core/');
define('ROUTES', __DIR__ . '/src/routes/');
define('WEB', __DIR__ . '/../web/');

//Auth
define('AUTH_METHOD', 'Bearer');                    //set 'Bearer' or Basic
define('ACCESS_T_EXPIRES', 3600);                   //access token lifetime in seconds. set to your preferences.

if( AUTH_METHOD === 'Bearer' ) {
    define('KEY', 'base64_encoded_string');         //change here if Bearer method
    define('SERVERNAME', 'server_name');            //change here if Bearer method
}

//System
date_default_timezone_set('UTC');


//exlusion method for authentications in format array(request=>array(methods)
//if in array can do request withoud auth
/*DEFAULT: 
    ['user' => ['post']] to permit login if no token exists
*/
$exclude_from_auth = [
    'user' => ['post']
];