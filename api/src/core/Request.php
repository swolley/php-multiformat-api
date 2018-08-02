<?php
namespace Api\Core;

class Request {
    private $method;
    private $resource;
    private $token;
    private $responseFormat;
    private $filters;
    private $parameters;
    private $others;    //temporary name

    public function __construct(){
        $this->parseRequest();
    }

    public function getMethod(){
        return $this->method;
    }

    public function getResource(){
        return $this->resource;
    }

    public function getToken(){
        return $this->token;
    }

    public function getResponseFormat(){
        return $this->responseFormat;
    }

    public function getFilters(){
        return $this->filters;
    }

    public function getParameters(){
        return $this->parameters;
    }

    public function getOthers(){
        return $this->others;
    }

    private function extractElements(array &$list, array $element_key) {
        $values = [];
        foreach($element_key as $key){
            if(isset($list[$key])){
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

    private function groupParameters($path_id = null) : array {
        $body_parameters = json_decode(file_get_contents('php://input'), TRUE) ?:[]; //body content
        
        if(!is_null($path_id)){
            $body_parameters['id'] = $path_id;
        }
        
        return array_merge($_GET, $body_parameters);
    }

    private function parseRequest() {
        $headers = apache_request_headers();
        $request_uri = array_diff(explode('/', explode('?', str_replace(API_URL, "", $_SERVER['REQUEST_URI']), 2)[0]), ["", "api"]);

        $this->method = strtolower($_SERVER['REQUEST_METHOD']); //http verb
        $this->resource = $this->extractElements($request_uri, [array_keys($request_uri)[0]]);  //resource path
        $this->token = $this->extractElements($headers, ['Authorization']);   //auth token
        $this->responseFormat = $this->extractElements($headers, ['Accept']);    //result format (json, html, xml, ecc)
        $this->filters = $this->extractElements($headers, ['X-Result', 'X-Total', 'X-From']);  //filters for pagination,
        $this->parameters = $this->groupParameters(!empty($request_uri) ? end($request_uri) : null);   //joined get, body parameters and optional id at the end of uri
        $this->others = $headers;    //remaining header's elements
    }
}