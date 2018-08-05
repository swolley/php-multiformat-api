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

    //getters/setters

    public function getContent(){
        return $this->content;
    }

    public function setContent($data){
        $this->content = $data;
    }

    public function hasContent() {
        return $this->content != null;
    }

    /**
     * response main method for correctly ended requests
     * @param   Request     $request        reference array
     * @param   int         $status         response http status (default is 200)
     * @return  mixed                       sub-methods contents depending on request's Accept tag (default is json)
     */
    public function ok(Request &$request, int $status = HttpStatusCode::OK) {
        switch($request->getResponseFormat()) {
            case 'text/html':
                return static::render("{$request->getMethod(method)}_{$request->getResource()}");
            case 'text/xml':
            case 'text/hal+xml':
                return static::xml($request, $status);  
            case 'application/json':
            case 'application/hal+json':
            default:
                return static::json($request, $status);
        }
    }

    /**
     * response method for anomalies
     * @param   string      $msg            error message
     * @return  int         $status         response error http status (default is 500)
     */
    public function error(Request &$request, \Exception $exc) {
        $this->content = $exc->getMessage();
        $status = $exc->getCode();
        
        return static::json($request, $status);
    }
    
    /**
     * injects data into templates if exist and return an html portion of page
     * @param   string      $view           template name
     * @return  string      $response       stringed and rendered html contents
     */
    private function render(string &$view) {
        header('Content-Type: text/html; charset=utf-8');
        ob_start();
        include (file_exists(WEB."${view}.php") ? WEB."${view}.php" : WEB.'error.php');
        $response = ob_get_contents();
        ob_end_clean();

        return $response;
    }

    /**
     * parse response data into json and if requested appends HAL data
     * @param   Request     $request        request infos
     * @param   int         $status         http response status
     * @return  string                      json encoded contents
     */
    private function json(Request &$request, int $status) {
        if($request->getResponseFormat() === 'application/hal+json'){
            header('Content-Type: application/hal+json; charset=utf-8', TRUE, $status);
            $hal_response = (new Resource())
                ->setURI("/{$request->getResource()}". (isset($request->getFilters()['id']) ? $request->getFilters()['id'] : ''))
                ->setLink($request->getResource(), new Link("/{$request->getResource()}"))
                ->setData($this->content);

            $writer = new Hal\JsonWriter(true);
            return $writer->execute($hal_response);
        } else {
            header('Content-Type: application/json; charset=utf-8', TRUE, $status);
            return json_encode($this->content, JSON_NUMERIC_CHECK);
        }
    }

    /**
     * parse response data into xml and if requested appends HAL data
     * @param   Request     $request        request infos
     * @param   int         $status         http response status
     * @return  mixed                       xml encoded contents
     */
    private function xml(Request &$request, int $status) {
        if($request->getResponseFormat() === 'text/hal+xml'){
            header('Content-Type: text/hal+xml; charset=utf-8', TRUE, $status);
            $hal_response = (new Resource())
                ->setURI("/{$request->getResource()}". (isset($request->getFilters()['id']) ? $request->getFilters()['id'] : ''))
                ->setLink($request->getResource(), new Link("/{$request->getResource()}"))
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

    /**
     * recoursive converter to xml
     * @param   mixed       $data           sub-data to convert
     * @param   mixed       $xml_data       main xml object
     */
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