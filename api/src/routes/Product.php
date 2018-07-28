<?php
namespace Api\Routes;
use Api\Core\RouteModel;

class Product extends RouteModel{
    public function  __construct(){
        parent::__construct();
    }

    public function update($params=[]) {
        throw new NotImplementedException();
    }
    
    public function post($params=[]) {
        throw new NotImplementedException();
    }

    public function delete($params=[]) {
        throw new NotImplementedException();
    }

    public function get($params=[]) {
        $result = isset($params['id'])
            ? $this->db->procedure('GetProduct', ['id' => $params['id']])
            : $this->db->procedure('GetAllProducts');

        
        return [
            'rowId' => 'id',    //TODO: don't like so much this solution to responde primary key
            'data' => isset($params['id']) && count($result) === 1
                ? $result[0]
                : $result
        ];
    }

    public function put($params=[]) {
        if(!isset($params['name'])){
            return 'Parameter missing';
        }

        return [
            'data' => $this->db->insert('products', [
                'name' => $params['name']
            ])
        ];
    }

    public function patch($params=[]) {
        throw new NotImplementedException();
    }
}