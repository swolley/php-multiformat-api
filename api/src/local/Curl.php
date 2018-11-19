<?php
namespace Api\Local;

class Curl {
    /**
     * make 
     */
    public function get(string $url, array $queryParams, $token=false) {
        $curl = curl_init();
        $url_data = http_build_query($queryParams);
        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;
        $http_header = ['Content-Type: application/json; boundary=' . $delimiter];
        if ($token) {
            $http_header[] = 'Authorization: Bearer ' . $token;
        }

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $http_header
        ]);
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
    
        //echo "CODE: {$info['http_code']}, RESPONSE: {$response}".PHP_EOL;
        $err = curl_error($curl);
        curl_close($curl);
        
        return $info['http_code'] == 200 ? $response : [
            'error' => $info['http_code'],
            'message' => $response
        ];
    }

    /**
     * make post call and return result
     * @param   string  $url        destination url
     * @param   array   $fields     textual data
     * @param   array   $pathNames  file's paths
     * @return  mixed               TRUE if ended correctly, array with error code and message if something went wrong
     */
    public function post(string $url, array $fields, array $pathNames, $token=false){
        $curl = curl_init();
        $url_data = http_build_query($fields);
        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;
        $files = $this->loadFileContents($pathNames);
        $post_data = $this->buildDataFiles($boundary, $fields, $files);
        $http_header = ['Content-Type: multipart/form-data; boundary=' . $delimiter];
        if ($token) {
            $http_header[] = 'Authorization: Bearer ' . $token;
        }

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => $http_header
        ]);
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
    
        //echo "CODE: {$info['http_code']}, RESPONSE: {$response}".PHP_EOL;
        $err = curl_error($curl);
        curl_close($curl);
        
        return $info['http_code'] == 201 ?: [
            'error' => $info['http_code'],
            'message' => $response
        ];
    }

    /**
     * returns content of passed files
     * @param   array    $pathNames     array of paths
     * @return  array    $files          array of contents
     */
    protected function loadFileContents(array &$pathNames) {
        $files = [];
        foreach ($pathNames as $path) {
            $splitted_path = explode('/', $path);
            $file_name = array_pop($splitted_path);
            $files[$file_name] = file_get_contents($path);
        }

        return $files;
    }

    /**
     * prepares data to be posted in multipart/form-data style
     * @param   string  $boundary   call identifier
     * @param   array   $fields     textual data
     * @param   array   $files      file content's array
     * @return  string  $data       formed post data string
     */
    protected function buildDataFiles(string &$boundary, array &$fields, array &$files) : string {
        $data = '';
        $eol = "\r\n";
        $delimiter = '-------------' . $boundary;

        foreach ($fields as $name => $content) {
            $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="' . $name . "\"" . $eol . $eol
                . $content . $eol;
        }

        foreach ($files as $name => $content) {
            $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $name . '"' . $eol
            . 'Content-Transfer-Encoding: binary' . $eol;
            $data .= $eol;
            $data .= $content . $eol;
        }
        $data .= "--" . $delimiter . "--" . $eol;

        return $data;
    }
}
