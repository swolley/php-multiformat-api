# php-multiformat-api
php api that handles json or html responses depending by the request.

## logic
1) client uses 'Accept' tag in headers during request and 'X-Auth-Token' for the token;
2) php api can handle html or json response format;
3) requests are REST-based. If client calls GET api/product, the backend search for <CLASS>-><HTTP METHOD> (ex. Product->get()). In case of html rendered request backend automatically searches for web/<HTTP METHOD>_<CLASS>.php (ex. web/get_product.php) file containing view content.

## classes
api is modular, all requests can be implemented in classes inside the 'libs' directory.

## frontend
index.html is only a test page.

## TODO
1) project is still in progress
2) handle login. Now I set token using localStorage.setItem('token', 'string') by browser console.
