<?php
namespace Api\Core;
use Api\Core\HttpStatusCode;
use PhpHal\Link;
use PhpHal\Resource;
use PhpHal\Format\Writer\Hal;


class Response{
    public static function ok(&$responseData, &$request) {
        return $request['responseFormat'] === 'text/html'
            ? (static::render($responseData, "{$request['method']}_{$request['resource']}"))
            : static::json($responseData, $request);
    }

    public static function error(string $responseMsg, int $status = HttpStatusCode::INTERNAL_SERVER_ERROR) {
        return static::json($responseMsg, $status);
    }
    
    private static function json(&$responseData, &$request, int $status = HttpStatusCode::OK) {
        if(HAL_RESP){
            header('Content-Type: application/hal+json; charset=utf-8', TRUE, $status);
            $halResponse = (new Resource())
                ->setURI("/{$request['resource']}". (isset($request['filter']['id']) ? $request['filter']['id'] : ''))
                ->setLink($request['resource'], new Link("/{$request['resource']}"))
                ->setData($responseData);

            $writer = new Hal\JsonWriter(true);
            return $writer->execute($halResponse);
        } else {
            header('Content-Type: application/json; charset=utf-8', TRUE, $status);
            return json_encode($responseData, JSON_NUMERIC_CHECK);
        }
    }

    private static function render(array &$data, string &$page) {
        header('Content-Type: text/html; charset=utf-8');
        ob_start();
        include (file_exists(WEB."${page}.php") ? WEB."${page}.php" : WEB.'error.php');
        $response = ob_get_contents();
        ob_end_clean();

        return $response;
    }
}