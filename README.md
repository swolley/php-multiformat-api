# php-multiformat-api
php api that handles json or html responses depending by the request;

## logic
1) client uses 'Accept' tag in headers during request and 'Authorization' for the token (implemented firebase\php-jwt package). Tokens are parsed in place and do not require a database table;
2) php api can handle html or json response format;
3) requests are REST-based and routes are all in the singular form, like the corresponding class. If client calls GET api/product, the backend search for <CLASS>-><HTTP METHOD> (ex. Product->get()). In case of html rendered request backend automatically searches for web/<HTTP METHOD>_<CLASS>.php (ex. web/get_product.php) file containing view content. Route names are always in singular form;
4) Database class can handle all crud queries and also a 'procedure' method for stored procedure calls;

## classes
'core' directory contains all the classes used by the system;
'local' can contains all custom classer not to be used as routes;
'routes' containes all the entities corresponsing to api's URIs. Routes classes must extend Api\Core\RouterModel abstract class;

## frontend
index.html is only a single page test file;

## todos
1) project is still in progress;
3) create an error's handler method;

## use
1) config.php file created by composer after 'install' command;
2) Routes classes must extend Api\Core\RouterModel abstract class. You can go to 'script' directory and call createRoute.php script to automatically create new routes with specified name, than implement them;
3) stored procedure parameters for mysal database need to be passed in same order as the defined procedure;

## credits
implemented use of jwt tokens from [firebase/php-jwt](https://github.com/firebase/php-jwt) repository (forked from the original [luciferous/jwt](https://github.com/luciferous/jwt) repository);

##license
[3-Clause BSD](https://opensource.org/licenses/BSD-3-Clause);
