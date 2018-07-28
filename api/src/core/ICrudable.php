<?php
namespace Api\Core;

interface ICrudable {
    function get($params=[]) : array;
    function post($params=[]);
    function delete($params=[]) : array;
    function update($params=[]) : array;
    function put($params=[]) : array;
    function patch($params=[]) : array;
}