<?php

interface ICrud {
    public function get($params=[]);
    public function post($params=[]);
    public function delete($params=[]);
    public function update($params=[]);
    public function put($params=[]);
}