<?php
namespace Api\Core;
use Api\Core\HttpStatusCode;
use PhpHal\Link;
use PhpHal\Resource;
use PhpHal\Format\Writer\Hal;


class Response {
    /**
     * response body content
     */
    private $content;
    /**
     * response http status code
     */
    private $status;
    /**
     * response content-type
     */
    private $format;
    /**
     * request route class name
     */
    private $resource;
    /**
     * request method
     */
    private $method;
    /**
     * request params
     */
    private $filters;
    
    public function __construct( ) {
    }

    //getters/setters

    public function setContent($data ) {
        $this->content = $data;
    }

    public function setStatus($data ) {
        $this->status = $data;
    }

    public function setFormat($data ) {
        $this->format = $data;
    }

    public function setResource($data ) {
        $this->resource = $data;
    }
    
    public function setMethod($data ) {
        $this->method = $data;
    }
    
    public function setFilters($data ) {
        $this->filters = $data;
    }

    /**
     * response alias method for correctly ended requests
     * @param   int         $status         response error http status (default is 200)
     * @return  mixed                       sub-methods contents depending on request's Accept tag (default is json)
     */
    public function prepare($data, int $status = HttpStatusCode::OK) {
        $this->content = $data;
        $this->status = $status;
    }

    /**
     * response alias method for anomalies
     * @param   string      $msg            error message
     * @param   int         $status         response error http status (default is 500)
     * @return  string                      response
     */
    public function error(string $msg = '', int $status = HttpStatusCode::INTERNAL_SERVER_ERROR) {
        $this->content = $msg;
        $this->status = $status;
        throw new \Exception($msg, $status);
    }

    /**
     * send response handler
     * @return  string                      response
     */
    public function send( ) {
        switch($this->format) {
            case 'text/html':
                return static::render();
            case 'text/xml':
            case 'text/hal+xml':
                return static::xml();  
            case 'application/json':
            case 'application/hal+json':
            default:
                return static::json();
        }
    }
    
    /**
     * injects data into templates if exist and return an html portion of page
     * @return  string      $response       stringed and rendered html contents
     */
    private function render() {
        header('Content-Type: text/html; charset=utf-8', TRUE);
        $view = "{$this->method}_{$this->resource}";
        if(file_exists(WEB."${view}.php")) {
            ob_start();
            include ( WEB."${view}.php");
            $response = ob_get_contents();
            ob_end_clean();
        } else {
            $status = HttpStatusCode::NOT_ACCEPTABLE;
            $content = 'No view found';
            $response = static::json();
        }

        return $response;
    }

    /**
     * parse response data into json and if requested appends HAL data
     * @return  string                      json encoded contents
     */
    private function json() {
        if( $this->format === 'application/hal+json' ) {
            header('Content-Type: application/hal+json; charset=utf-8', TRUE, $this->status);
            $hal_response = (new Resource())
                ->setURI("/{$this->resource}". (isset($this->filters['id']) ? $this->filters['id'] : ''))
                ->setLink($this->resource, new Link("/{$this->resource}"))
                ->setData($this->content);

            $writer = new Hal\JsonWriter(true);
            return $writer->execute($hal_response);
        } else {
            header('Content-Type: application/json; charset=utf-8', TRUE, $this->status);
            return json_encode($this->content, JSON_NUMERIC_CHECK);
        }
    }

    /**
     * parse response data into xml and if requested appends HAL data
     * @return  mixed                       xml encoded contents
     */
    private function xml() {
        if( $this->format === 'text/hal+xml' ) {
            header('Content-Type: text/hal+xml; charset=utf-8', TRUE, $this->status);
            $hal_response = (new Resource())
                ->setURI("/{$this->resource}". (isset($this->filters['id']) ? $this->filters['id'] : ''))
                ->setLink($this->resource, new Link("/{$this->resource}"))
                ->setData($this->content);

            $writer = new Hal\XmlWriter(true);
            
            return $writer->execute($hal_response);
        } else {
            header('Content-Type: text/xml; charset=utf-8', TRUE, $this->status);
            $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><data></data>');
            static::array_to_xml($this->content,$xml_data);
            
            return $xml_data->asXML();
        }
        
    }

    /**
     * recoursive converter to xml
     * @param   mixed       $data           sub-data to convert
     * @param   mixed       $xml_data       main xml object
     */
    private function array_to_xml( $data, &$xml_data ) {
        foreach( $data as $key => $value ) {
            if(  is_numeric($key)  ) {
                $key = 'item'.$key; //dealing with <0/>..<n/> issues
            }
            if(  is_array($value) ) {
                $subnode = $xml_data->addChild($key);
                static::array_to_xml($value, $subnode);
            } else {
                $xml_data->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }
}