<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/REST_Controller.php';
require_once APPPATH . '/libraries/JWT.php';
require_once APPPATH . '/libraries/BeforeValidException.php';
require_once APPPATH . '/libraries/ExpiredException.php';
require_once APPPATH . '/libraries/SignatureInvalidException.php';

use Epermute\Libraries\REST_Controller;
use \Firebase\JWT\JWT;

class MY_Controller extends REST_Controller
{
	private $user_credential;
    public function auth()
    {
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
        //JWT Auth middleware
        $headers = $this->input->get_request_header('Authorization');
        $key = $this->config->item('GPCINQDASUVCI_jwt_key');
        $token= "token";
       	if (!empty($headers)) {
        	if (preg_match('/Bearer\s(\S+)/', $headers , $matches)) {
            $token = $matches[1];
        	}
    	}
        try {
            
           $decoded = JWT::decode($token, $key, array('HS256'));

        } catch (Exception $e) {
            $invalid = [
                'status' => $e->getCode(),
            // 'messageGet'=>$e->getMessage(),
                'message'=>'TOKEN INVALIDE OU EXPIRE !'
            ];
            $this->response($invalid, 401);//401
        }
    }
}