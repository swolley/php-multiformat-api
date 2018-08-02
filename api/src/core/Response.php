<?php
namespace Api\Core;
use Api\Core\HttpStatusCode;
use PhpHal\Link;
use PhpHal\Resource;
use PhpHal\Format\Writer\Hal;


class Response {
    private $content;

    public function __construct(){
    }

    public function getContent(){
        return $this->content;
    }

    public function setContent($data){
        $this->content = $data;
    }

    public function isContent() {
        return $this->content != null;
    }

    public function ok(Request &$request) {
        switch($request->getResponseFormat()) {
            case 'text/html':
                return static::render("{$request['method']}_{$request['resource']}");
            case 'text/xml':
            case 'text/hal+xml':
                return static::xml($request);  
            case 'application/json':
            case 'application/hal+json':
                return static::json($request);
        }
    }

    public function error(string $msg, int $status = HttpStatusCode::INTERNAL_SERVER_ERROR) {
        $this->content = $msg;
        return static::json($status);
    }
    
    private function render(string &$page) {
        header('Content-Type: text/html; charset=utf-8');
        ob_start();
        include (file_exists(WEB."${page}.php") ? WEB."${page}.php" : WEB.'error.php');
        $response = ob_get_contents();
        ob_end_clean();

        return $response;
    }

    private function json(Request &$request, int $status = HttpStatusCode::OK) {
        if($request->getResponseFormat() === 'application/hal+json'){
            header('Content-Type: application/hal+json; charset=utf-8', TRUE, $status);
            $hal_response = (new Resource())
                ->setURI("/{$request['resource']}". (isset($request['filter']['id']) ? $request['filter']['id'] : ''))
                ->setLink($request['resource'], new Link("/{$request['resource']}"))
                ->setData($this->content);

            $writer = new Hal\JsonWriter(true);
            return $writer->execute($hal_response);
        } else {
            header('Content-Type: application/json; charset=utf-8', TRUE, $status);
            return json_encode($this->content, JSON_NUMERIC_CHECK);
        }
    }

    private function xml(Request &$request) {
        if($request['response_format'] === 'text/hal+xml'){
            header('Content-Type: text/hal+xml; charset=utf-8', TRUE);
            $hal_response = (new Resource())
                ->setURI("/{$request['resource']}". (isset($request['filter']['id']) ? $request['filter']['id'] : ''))
                ->setLink($request['resource'], new Link("/{$request['resource']}"))
                ->setData($this->content);

            $writer = new Hal\XmlWriter(true);
            
            return $writer->execute($hal_response);
        } else {
            header('Content-Type: text/xml; charset=utf-8', TRUE);
            $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><data></data>');
            static::array_to_xml($this->content,$xml_data);
            
            return $xml_data->asXML();
        }
        
    }

    private function array_to_xml( $data, &$xml_data ) {
        foreach( $data as $key => $value ) {
            if( is_numeric($key) ){
                $key = 'item'.$key; //dealing with <0/>..<n/> issues
            }
            if( is_array($value) ) {
                $subnode = $xml_data->addChild($key);
                static::array_to_xml($value, $subnode);
            } else {
                $xml_data->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }
}