<?php
namespace Api\Core;

interface ICrudable {
    function get($params=[]);
    function post($params=[]);
    function delete($params=[]);
    function update($params=[]);
    function put($params=[]);
    function patch($params=[]);
}