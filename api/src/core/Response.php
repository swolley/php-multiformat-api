<?php
namespace Api\Core;
use Api\Core\HttpStatusCode;

class Response{
    public static function ok(&$responseData, string &$format, string $requestedResourse) {
        return $format === 'text/html'
            ? (static::render($responseData, $requestedResourse))
            : static::json($responseData);
    }

    public static function error(string $responseMsg, int $status = HttpStatusCode::INTERNAL_SERVER_ERROR) {
        return static::json($responseMsg, $status);
    }
    
    private static function json(&$responseData, int $status = HttpStatusCode::OK) {
        header('Content-Type: application/json', TRUE, $status);
        return json_encode($responseData);
    }

    private static function render(array &$data, string &$page) {
        header('Content-Type: text/html');
        ob_start();
        include (file_exists(WEB."${page}.php") ? WEB."${page}.php" : WEB.'error.php');
        $response = ob_get_contents();
        ob_end_clean();

        return $response;
    }
}