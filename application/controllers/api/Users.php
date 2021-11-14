<?php
//use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

use Epermute\Libraries\REST_Controller;
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
class Users extends MY_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->auth();
        $this->load->model('user_model','UserModel');

    }

    /*
    * Get all users : Method get
    */
    public function index_get($parm='')
    {

        if (empty($parm)) {
            $data =  $this->UserModel->fetch_all_users();
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
            $data =  $this->UserModel->get_user($parm);
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


    /**
     * Create new user
     * @method:POST
     */
    
    public function register_post()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);
        // $_POST = $this->security->xss_clean($_POST);

        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|is_unique[aqi_pp_users.mobile]',
            array('is_unique'=>'Ce numero de telephone existe déja !')
        );
        $this->form_validation->set_rules('email', 'Email', 'trim|is_unique[aqi_pp_users.email]|valid_email',
            array('is_unique'=>'Cet email existe déja !')
        );
        $this->form_validation->set_rules('login', 'Login', 'trim|is_unique[aqi_pp_users.login]',
            array('is_unique'=>'Ce Login existe déja !')
        );

       if ($this->form_validation->run() == FALSE)
       {
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
       }
       else
       {
           $data = $this->input->post();
           $data['modified'] = date('Y-m-d\TH:i:s.u');
           $data['registred '] = date('Y-m-d\TH:i:s.u');
           $data['code'] = time();
           $data['password'] = $this->encrypt_pwd($this->input->post('password'));

           $outpout = $this->UserModel->insert_user($data);
           if ($outpout>0 AND !empty($outpout)) {
               $message = [
                   'status'=>201,
                   'message'=>"Utilisateur créé avec succes!"
               ];

               $this->response($message, REST_Controller::HTTP_CREATED);
               
           } else {
            $message = [
                'status'=>400,
                'message'=>"Une erreur est survenue lors de l'enregistrement!"
            ];

            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
           }
       }
    }

    /** 
     * Login user
     * @method: POST
    */

    public function login_post()
    {
        $key = $this->config->item('PHONEPLUS_jwt_key');
        // $_POST = $this->security->xss_clean($_POST);
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('login', 'Login', 'trim|required');

       if ($this->form_validation->run() == FALSE)
       {
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        } else{
            $outpout = $this->UserModel->user_login($this->input->post('login'),$this->encrypt_pwd($this->input->post('password')));
            if (!empty($outpout) AND $outpout != FALSE) {
                $token = array();
                $date = new DateTime();
                $token['id'] = $outpout->id;
				$token['email'] = $outpout->email;
                $token['nickname'] = $outpout->nickname;                
                $token['image'] = $outpout->photo;
                $token['role'] = $outpout->role;
                $token['iat'] = $date->getTimestamp();
                $token['exp'] = $date->getTimestamp() + 60*60*5;
                $token['time'] = time();
                $token['sub'] = $outpout->id;

                $user_data = [
                    'code'=>$outpout->code,
                    'nom'=>$outpout->nom,
                    'prenom'=>$outpout->prenom,
                    'image'=>$outpout->photo,
                    'url'=>base_url(),
                    'role'=>$outpout->role
                ];
                
                $user_data['token'] = JWT::encode($token,$key);

                $message = [
                    'status'=>200,
                    'data'=>$user_data,
                    'message'=>"Authentification réuissie"
                ];

                $this->response($message, REST_Controller::HTTP_OK);
        
            } else {
                $message = [
                    'status'=>400,
                    'message'=>"Une erreur est survenue lors de l'authentification !"
                ];

                $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
            }
        }

    }

    public function encrypt_pwd($pwd){
		for($i=0; $i<1000; $i++){
			$pass_in_md5 = md5($pwd);
			$pass_in_sh = sha1($pass_in_md5);
			$pass = $pass_in_sh;
		}
		return $pass;
	}
    
    /**
     * Confirm User
     * @method: GET
     */

    public function confirm_get($user, $key)
    {
        $outpout = $this->UserModel->user_check($user, $key);
        var_dump($outpout);
    }
    
    public function upload_image(){
        
    }
}