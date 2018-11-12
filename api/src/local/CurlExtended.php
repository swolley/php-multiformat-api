<?php
namespace Libs;

use Libs\Curl;
/*
$fields = [
    "caption" => string,
    "priority" => integer,
    "time" => dateTime,
    "author" => string,
    "idnews" => integer,
    "category" => string,
    "headline" => string,
    "text" => string
];

$pathNames = [
    strings
];
*/

class CurlExtended extends Curl {
    /** 
     * post alias with file deletion
     * @param   string  $url                destination url
     * @param   array   $fields             body object
     * @param   array   $pathNames          array of files' path
     * @param   bool    $deleteAfterPost    deletes local files if post ended correctly
     * @return  mixed   $response           post response
     * @see             $this->post
     * */
	public function postMulti(string $url, array $fields, array $pathNames, bool $deleteAfterPost = FALSE){
		$response = $this->post($url, $fields, $pathNames);
		if($response === TRUE && $deleteAfterPost) {
			$this->deleteLocalFiles($pathNames);
		}
		
		return $response;
	}
    
    /** 
     * post alias with no fields and file deletion
     * @param   string  $url                destination url
     * @param   array   $pathNames          array of files' path
     * @param   bool    $deleteAfterPost    deletes local files if post ended correctly
     * @return  mixed   $response           post response
     * @see             $this->post
     * */
	public function postFiles(string $url, array $pathNames, bool $deleteAfterPost = FALSE){
		$response = $this->post($url, [], $pathNames);
		if($response === TRUE && $deleteAfterPost) {
			$this->deleteLocalFiles($pathNames);
		}
		
		return $response;
	}
    
    /** 
     * post alias with no files and file deletion
     * @param   string  $url                destination url
     * @param   array   $fields             body object
     * @return  mixed   $response           post response
     * @see             $this->post
     * */
	public function postFields(string $url, array $fields){
		return $this->post($url, $fields, []);
	}

    /**
	 * deletes local files. called after post finishes
	 * @param	array	$file	array of paths
	 */
	protected function deleteLocalFiles(array $files) {
		foreach($files as $file) {
			unlink($file);
		}
	}
}
