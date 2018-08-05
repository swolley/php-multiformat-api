<?php
namespace Api\Core;
use Api\Core\Request;

interface ICrudable {
    /**
     * method that triggered by GET http verb
     * @param   Request $request        (optional) assoc array with paramter's names and relative values
     * @return  mixed                   defined by user
     */
    function get(Request &$request);
    
    /**
     * method that triggered by POST http verb
     * @param   Request $request        (optional) assoc array with paramter's names and relative values
     * @return  mixed                   defined by user
     */
    function post(Request &$request);
    
    /**
     * method that triggered by DELETE http verb
     * @param   Request $request        (optional) assoc array with paramter's names and relative values
     * @return  mixed                   defined by user
     */
    function delete(Request &$request);
    
    /**
     * method that triggered by UPDATE http verb
     * @param   Request $request        (optional) assoc array with paramter's names and relative values
     * @return  mixed                   defined by user
     */
    function put(Request &$request);
    
    /**
     * method that triggered by PATCH http verb
     * @param   Request $request        (optional) assoc array with paramter's names and relative values
     * @return  mixed                   defined by user
     */
    function patch(Request &$request);
}