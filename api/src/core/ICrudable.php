<?php
namespace Api\Core;

interface ICrudable {
    /**
     * method that triggered by GET http verb
     * @param   array   $params         (optional) assoc array with paramter's names and relative values
     * @return  mixed                   defined by user
     */
    function get($params=[]);
    /**
     * method that triggered by POST http verb
     * @param   array   $params         (optional) assoc array with paramter's names and relative values
     * @return  mixed                   defined by user
     */
    function post($params=[]);
    /**
     * method that triggered by DELETE http verb
     * @param   array   $params         (optional) assoc array with paramter's names and relative values
     * @return  mixed                   defined by user
     */
    function delete($params=[]);
    /**
     * method that triggered by UPDATE http verb
     * @param   array   $params         (optional) assoc array with paramter's names and relative values
     * @return  mixed                   defined by user
     */
    function update($params=[]);
    /**
     * method that triggered by PUT http verb
     * @param   array   $params         (optional) assoc array with paramter's names and relative values
     * @return  mixed                   defined by user
     */
    function put($params=[]);
    /**
     * method that triggered by PATCH http verb
     * @param   array   $params         (optional) assoc array with paramter's names and relative values
     * @return  mixed                   defined by user
     */
    function patch($params=[]);
}