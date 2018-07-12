<?php
namespace Api\Core;

interface ICrudable {
    function get($params=[]) : array;
    function post($params=[]) : array;
    function delete($params=[]) : array;
    function update($params=[]) : array;
    function put($params=[]) : array;
}