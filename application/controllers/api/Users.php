<?php
//use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

use Phoneplus\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          AQUICK'INTL
 * @license         MIT
 * @link            https://www.aquickintl.com
 */
class Users extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('user_model');

    }

    /*
    * Get all users : Method get
    */
    public function users_get($parm='')
    {
        header("Access-Control-Allow-Origin: *");
        if (empty($parm)) {
            $data =  $this->user_model->fetch_all_users();
            if (empty($users)) {
                $this->set_response([
                    'status'=>false,
                    'message'=>'Aucun enregitrement trouvé'
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($data, REST_Controller::HTTP_OK);
            }
            
        } else {
            $data =  $this->user_model->get_user($parm);
            if (empty($users)) {
                $this->set_response([
                    'status'=>false,
                    'message'=>'Aucun enregitrement trouvé'
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($data, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response($data, REST_Controller::HTTP_OK);
    }
    /*
    * Create new user : Method post
    */
    public function users_post()
    {
       //
    }
    
}