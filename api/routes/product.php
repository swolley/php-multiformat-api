<?php

class Product{
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function get($params=array()){
        return [
            'data' => isset($params['id']) ? 
                $this->db->procedure("GetProduct", ['id' => $params['id']]) : 
                $this->db->procedure("GetAllProducts")
        ];
    }

    public function put($params=array()){
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