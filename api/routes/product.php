<?php
namespace Routes;

class Product implements ICrud{
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function update($params=[]){
        throw new NotImplementedException();
    }
    
    public function post($params=[]){
        throw new NotImplementedException();
    }

    public function delete($params=[]){
        throw new NotImplementedException();
    }

    public function get($params=[]){
        return [
            'rowId' => 'id',
            'data' => isset($params['id']) ? 
                $this->db->procedure("GetProduct", ['id' => $params['id']]) : 
                $this->db->procedure("GetAllProducts")
        ];
    }

    public function put($params=[]){
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