<?php
namespace Api\Routes;
use Api\Core\RouteModel;
use Api\Core\Request;
use Api\Core\Response;
use Api\Core\HttpStatusCode;

final class User extends RouteModel {
	use PrimaryReader { getPrimaryName as private; }

    public function  __construct( ) {
        parent::__construct();
    }

    public function put(Request &$request, Response &$response) {
        throw new NotImplementedException('No method found', HttpStatusCode::NOT_IMPLEMENTED);
    } 

    public function get(Request &$request, Response &$response) {
		$params = $request->getParameters();
        
        $result = isset($params['id'])
            ? $this->db->procedure('GetUser', ['id' => $params['id']])
            : $this->db->procedure('GetAllUsers');

        $response->prepare([
            'rowId' => $this->getPrimaryName(),
            'data' => isset($params['id']) && count($result) === 1
                ? $result[0]
                : $result
        ]);
    }

    public function post(Request &$request, Response &$response) {
        throw new NotImplementedException('No method found', HttpStatusCode::NOT_IMPLEMENTED);
    }

    public function delete(Request &$request, Response &$response) {
        throw new NotImplementedException('No method found', HttpStatusCode::NOT_IMPLEMENTED);
    }

    public function patch(Request &$request, Response &$response) {
        throw new NotImplementedException('No method found', HttpStatusCode::METHOD_NOT_ALLOWED);
    }
}