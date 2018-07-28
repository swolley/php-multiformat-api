<?php
//errors and debug
define('DEBUG_MODE', TRUE);                         //set true during debug, else false

error_reporting(DEBUG_MODE ? E_ALL : NULL);
ini_set('display_errors', DEBUG_MODE ? 1 : 0);

//Database
define('DB_TYPE', 'mysql');                         //change here
define('DB_HOST', 'localhost');                     //change here
define('DB_NAME', 'temp');                          //change here
define('DB_USER', 'root');                          //change here
define('DB_PASS', 'test');                          //change here

//App
define('API_URL', 'phpApi/api');
define('CORE', __DIR__ . '/src/core/');
define('ROUTES', __DIR__ . '/src/routes/');
define('WEB', __DIR__ . '/../web/');

//Auth
define('AUTH_METHOD', 'Bearer');    //set 'Bearer' (jwt libraries already installed) or 'Basic' (credentials)

if(AUTH_METHOD === 'Bearer'){
    define('KEY', 'N91vfWUpcmj1KvCmfrS8V9vu8gKD21LsslH0wE0CrdCAm5GVq3vpR4TACy6qb+/0+aFr23WOuluA1jKVrqHknA==');    //change here if Bearer method
    define('SERVERNAME', 'my_api_server');              //change here if Bearer method
}

//exlusion method for authentications in format array(request=>array(methods)
//if in array can do request withoud auth
/*DEFAULT: 
    ['user' => ['post']] to permit login if no token exists
*/
$excludeFromAuth = [
    'user' => ['post']
];