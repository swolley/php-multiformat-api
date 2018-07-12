<?php
namespace Core;

class Response{
    public static function send($msg, $response = 'OK', $status = 200){
        header('Content-Type: application/json', true, $status);
        exit(json_encode(array_merge([
                'response' => $response,
                'status' => $status,
            ],
            $msg
        )));
    }

    public static function error($msg, $status=500){
        static::send([
                'error' => $msg
            ],
            'ERROR',
            $status
        );
    }

    public static function render($data, $page){
        header('Content-Type: text/html');
        ob_start();
        include (file_exists(WEB."${page}.php") ? WEB."${page}.php" : WEB."error.php");
        $response = ob_get_contents();
        ob_end_clean();

        exit($response);
    }
}