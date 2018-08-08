<?php
namespace Api\Routes;
use Api\Core\RouteModel;
use Api\Core\Request;
use Api\Core\Response;
use Api\Core\HttpStatusCode;

class Product extends RouteModel{
    public function  __construct( ) {
        parent::__construct();
    }
    
    public function post(Request &$request, Response &$response) {
        throw new NotImplementedException('No method found', HttpStatusCode::NOT_IMPLEMENTED);
    }

    public function delete(Request &$request, Response &$response) {
        throw new NotImplementedException('No method found', HttpStatusCode::NOT_IMPLEMENTED);
    }

    public function get(Request &$request, Response &$response) {
        $params = $request->getParameters();
        
        $result = isset($params['id'])
        ? $this->db->procedure('GetProduct', ['id' => $params['id']])
        : $this->db->procedure('GetAllProducts');
        
        $response->prepare([
            'rowId' => 'id',    //TODO: don't like so much this solution to responde primary key
            'data' => isset($params['id']) && count($result) === 1
            ? $result[0]
            : $result
        ]);
    }
    
    public function put(Request &$request, Response &$response) {
        $params = $request->getParameters();
        
        if( !isset($params['name']) ) {
            throw new \BadMethodCallException("Missing parameters", HttpStatusCode::BAD_REQUEST);
        }
        
        $response->prepare([
            'data' => $this->db->insert('products', [
                'name' => $params['name']
            ])
        ]);
    }
        
    public function patch(Request &$request, Response &$response) {
        throw new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);
    }
}