<?php
namespace Api\Routes;
use Api\Core\RouteModel;

class Product extends RouteModel{
    public function  __construct(){
        parent::__construct();
    }

    public function update($params=[]) : array {
        throw new NotImplementedException();
    }
    
    public function post($params=[]) {
        throw new NotImplementedException();
    }

    public function delete($params=[]) : array {
        throw new NotImplementedException();
    }

    public function get($params=[]) : array {
        return [
            'rowId' => 'id',    //TODO: don't like so much this solution to responde primary key
            'data' => isset($params['id']) ? 
                $this->db->procedure('GetProduct', ['id' => $params['id']]) : 
                $this->db->procedure('GetAllProducts')
        ];
    }

    public function put($params=[]) : array {
        if(!isset($params['name'])){
            return 'Parameter missing';
        }

        return [
            'data' => $this->db->insert('products', [
                'name' => $params['name']
            ])
        ];
    }

    public function patch($params=[]) : array {
        throw new NotImplementedException();
    }
}