# php-multiformat-api
php api that handles json or html responses depending by the request.

## logic
1) client uses 'Accept' tag in headers during request and 'X-Auth-Token' for the token;
2) php api can handle html or json response format;
3) requests are REST-based. If client calls GET api/product, the backend search for <CLASS>-><HTTP METHOD> (ex. Product->get()). In case of html rendered request backend automatically searches for web/<HTTP METHOD>_<CLASS>.php (ex. web/get_product.php) file containing view content. Route names are always in singular form.
4) Database class can handle all crud queries and also a procedure method for stored procedure calls.

## classes
api is modular, all requests can be implemented in classes inside the 'libs' directory.

## frontend
index.html is only a test page.

## todos
1) project is still in progress
2) implement JWT

##use
1) rename config.php.example into config.php and fill db infos
2) create needed route classes in api/routes folder implementing ICrud interface
3) stored procedure parameters for mysal database need to be passed in same order as the defined procedure 

##credits
implemented use of jwt tokens from firebase/php-jwt repository (forked from the original luciferous/jwt repository)
