<?php
namespace Api\Core;
use Api\Core\HttpStatusCode;
use Api\Core\Response;

class Request {
    /**
     * request http verb
     */
    private $method;
    /**
     * route class name
     */
    private $resource;
    /**
     * auth token
     */
    private $token;
    /**
     * response format (json, html, xml, ecc)
     */
    private $responseFormat;
    /**
     * request format (json, html, xml, etc)
     */
    private $requestFormat;
    /**
     * filters for pagination, etc
     */
    private $filters;
    /**
     * joined get, body parameters and optional id at the end of request's uri
     */
    private $parameters;
    /**
     * temporary name with remainig header tags
     */
    private $others;

    public function __construct(Response &$response) {
        $this->parseRequest($response);
    }

    //only get methods exposed outside the class

    public function getMethod( ) {
        return $this->method;
    }

    public function getResource( ) {
        return $this->resource;
    }

    public function getToken( ) {
        return $this->token;
    }

    public function getResponseFormat( ) {
        return $this->responseFormat;
    }

    public function getRequestFormat( ) {
        return $this->requestFormat;
    }

    public function getFilters( ) {
        return $this->filters;
    }

    public function getParameters( ) {
        return $this->parameters;
    }

    public function getOthers( ) {
        return $this->others;
    }

    /**
     * removes elements from array and returns them
     * @param   array   $list           reference array
     * @return  mixed                   requested elements or null
     */
    private function extractElements(array &$list, array $element_key) {
        $values = [];
        foreach($element_key as $key ) {
            if( isset($list[$key]) ) {
                array_push($values, $list[$key]);
                unset($list[$key]);
            }
        }

        return $values !== []
            ? (count($values) === 1
                ? $values[0]
                : $values)
            : NULL;
    }

    /**
     * groups get and post data and returns them as a unique associative array
     * @param   mixed   $path_id        (optional) id from path if single element requested
     * @return  array                   ensemble of all parameters
     */
    private function groupParameters($path_id = null) : array {
        $body_parameters = $this->requestFormat === 'application/json'
            ? file_get_contents('php://input') 
            : json_encode(simplexml_load_string(file_get_contents('php://input'))); //body content
        
        $body_parameters = json_decode($body_parameters,TRUE) ?: [];
        
        if( !is_null($path_id) ) {
            $body_parameters['id'] = $path_id;
        }
        
        return array_merge($_GET, $body_parameters);
    }

    /**
     * parse request's info and passed parameters and sets Request properties
     */
    private function parseRequest(Response &$response) {
        $headers = apache_request_headers();
        $request_uri = array_diff(explode('/', explode('?', str_replace(API_URL, '', $_SERVER['REQUEST_URI']), 2)[0]), ['', 'api']);

        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->resource = !empty($request_uri) ? $this->extractElements($request_uri, [array_keys($request_uri)[0]]) : null;
        $this->token = $this->extractElements($headers, ['Authorization']);
        $this->responseFormat = $this->extractElements($headers, ['Accept']);
        $this->requestFormat = $this->extractElements($headers, ['Content-Type']);
        $this->filters = $this->extractElements($headers, ['X-Result', 'X-Total', 'X-From']);
        $this->parameters = $this->groupParameters(!empty($request_uri) ? end($request_uri) : null);
        $this->others = $headers;    //remaining header's elements

        //don't like so much
        $response->setFormat($this->responseFormat);
        $response->setFilters($this->filters);
        $response->setResource($this->resource);
        $response->setMethod($this->method);
    }
}