<?php

class Product{
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function get($params=array()){
        return [
            'data' => $this->db->select("SELECT * from products" . (isset($params['id']) ? " WHERE :id=".$params['id'] : ""))
        ];
    }

    public function post($params=array()){
        if(!isset($params['name'])){
            return "Parameter missing";
        }

        return [
            'data' => $this->db->insert("products", [
                'name' => $params['name']
            ])
        ];
    }
}