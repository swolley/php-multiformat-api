<?php
namespace Api\Core;

class HttpStatusCode {
    //1xx Informational
    const SWITCHING_PROTOCOLS = 101;

    // /2xx Success
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NONAUTHORITATIVE_INFORMATION = 203;
    const NO_CONTENT = 204;                         //no body to response
    const RESET_CONTENT = 205;
    const PARTIAL_CONTENT = 206;
    
    //3xx Redirection
    const MULTIPLE_CHOICES = 300;
    const MOVED_PERMANENTLY = 301;
    const FOUND = 302;                              //redirection
    const SEE_OTHER = 303;
    const NOT_MODIFIED = 304;                       //no body to response (same value found, not updated)
    const USE_PROXY = 305;
    const TEMPORARY_REDIRECT = 307;                 //not processed. Responde with correct uri
    
    //4xx Client Error
    const BAD_REQUEST = 400;                        //no correct syntax or parameter's values, deceptive request routing
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIRED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;                          //can't map request to resource
    const METHOD_NOT_ALLOWED = 405;                 //no method for request's verb (readonly for ex.)
    const NOT_ACCEPTABLE = 406;                     //can't return requested response type (only json, not text, etc.)
    const PROXY_AUTHENTICATION_REQUIRED = 407;
    const REQUEST_TIMEOUT = 408;
    const CONFLICT = 409;
    const GONE = 410;
    const LENGTH_REQUIRED = 411;
    const PRECONDITION_FAILED = 412;
    const REQUEST_ENTITY_TOO_LARGE = 413;
    const request_uri_TOO_LARGE = 414;
    const UNSUPPORTED_MEDIA_TYPE = 415;
    const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const EXPECTATION_FAILED = 417;
    const IM_A_TEAPOT = 418;
    
    //5xx Server Error
    const INTERNAL_SERVER_ERROR = 500;              //exceptions, unhandled error
    const NOT_IMPLEMENTED = 501;                    //no method found (at the moment)
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
}