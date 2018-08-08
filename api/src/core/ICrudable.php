<?php
namespace Api\Core;
use Api\Core\Request;

interface ICrudable {
    /**
     * method that triggered by GET http verb
     * @param   Request     $request        reuest object reference
     * @param   Response    $response       response object reference
     */
    function get(Request &$request, Response &$response);
    
    /**
     * method that triggered by POST http verb
     * @param   Request     $request        reuest object reference
     * @param   Response    $response       response object reference
     */
    function post(Request &$request, Response &$response);
    
    /**
     * method that triggered by DELETE http verb
     * @param   Request     $request        reuest object reference
     * @param   Response    $response       response object reference
     */
    function delete(Request &$request, Response &$response);
    
    /**
     * method that triggered by UPDATE http verb
     * @param   Request     $request        reuest object reference
     * @param   Response    $response       response object reference
     */
    function put(Request &$request, Response &$response);
    
    /**
     * method that triggered by PATCH http verb
     * @param   Request     $request        reuest object reference
     * @param   Response    $response       response object reference
     */
    function patch(Request &$request, Response &$response);
}