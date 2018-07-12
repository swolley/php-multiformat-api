<?php
namespace Api\Core;
use Api\Core\HttpStatusCode;

class Response{
    public static function send(array $msg, string $response = 'OK', int $status = HttpStatusCode::OK) : void {
        header('Content-Type: application/json', true, $status);
        exit(json_encode(array_merge([
                'response' => $response,
                'status' => $status,
            ],
            $msg
        )));
    }

    public static function error(string $msg, int $status=HttpStatusCode::INTERNAL_SERVER_ERROR) : void {
        static::send([
                'error' => $msg
            ],
            'ERROR',
            $status
        );
    }

    public static function render(array $data, string $page) : void {
        header('Content-Type: text/html');
        ob_start();
        include (file_exists(WEB."${page}.php") ? WEB."${page}.php" : WEB."error.php");
        $response = ob_get_contents();
        ob_end_clean();

        exit($response);
    }
}