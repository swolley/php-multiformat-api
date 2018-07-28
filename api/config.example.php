<?php
//errors and debug
define('DEBUG_MODE', TRUE);                         //set true during debug else false

error_reporting(DEBUG_MODE ? E_ALL : NULL);
ini_set('display_errors', DEBUG_MODE ? 1 : 0);

//Database
define('DB_TYPE', 'dbtype');                        //change here
define('DB_HOST', 'host');                          //change here
define('DB_NAME', 'dbname');                        //change here
define('DB_USER', 'dbuser');                        //change here
define('DB_PASS', 'dbpassword');                    //change here

//App
define('API_URL', 'phpApi/api');
define('CORE', __DIR__ . '/src/core/');
define('ROUTES', __DIR__ . '/src/routes/');
define('WEB', __DIR__ . '/../web/');

//Auth
define('AUTH_METHOD', 'Bearer');    //set 'Bearer' or Basic

if(AUTH_METHOD === 'Bearer'){
    define('KEY', 'base64_encoded_string');    //change here if Bearer method
    define('SERVERNAME', 'server_name');       //change here if Bearer method
}

//exlusion method for authentications in format array(request=>array(methods)
//if in array can do request withoud auth
/*DEFAULT: 
    ['user' => ['post']] to permit login if no token exists
*/
$excludeFromAuth = [
    'user' => ['post']
];