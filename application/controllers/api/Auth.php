<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use \Firebase\JWT\JWT;
use Phoneplus\Libraries\REST_Controller;
require APPPATH . 'libraries/Format.php';
// require APPPATH . '/libraries/REST_Controller.php';
// require APPPATH . 'libraries/JWT.php';

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
class Auth extends MY_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('user_model','UserModel');

    }
    
    public function register_post()
    {

        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|is_unique[aqi_pp_users.mobile]',
            array('is_unique'=>'Ce numero de telephone existe déja !')
        );
        $this->form_validation->set_rules('email', 'Email', 'trim|is_unique[aqi_pp_users.email]|valid_email',
            array('is_unique'=>'Cet email existe déja !')
        );
        $this->form_validation->set_rules('login', 'Login', 'trim|is_unique[aqi_pp_users.login]',
            array('is_unique'=>'Cet Login existe déja !')
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
           $data['password'] = password_hash($this->encrypt_pwd($this->input->post('password'), PASSWORD_BCRYPT));
           $data['activation_key'] = $this->code_confirm();

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
        } else
        {
            $outpout = $this->UserModel->user_login($this->input->post('login'),$this->encrypt_pwd($this->input->post('password')));
            if (!empty($outpout) AND $outpout != FALSE) 
            {
                $token = array();
                $date = new DateTime();
                $token['iat'] = $date->getTimestamp();
                $token['exp'] = $date->getTimestamp() + 60*60*5;
                $token['time'] = time();
                $token['sub'] = $outpout->role;

                $user_data = [
                    'id'=>$outpout->id,
                    'code'=>$outpout->code,
                    'nom'=>$outpout->nom,
                    'prenom'=>$outpout->prenom,
                    'image'=>$outpout->photo,
                    'email'=>$outpout->email,
                    'nickname'=>$outpout->nickname
                ];
                
                $data['token'] = JWT::encode($token,$key);
                $data['user'] = $user_data;
                
                $message = [
                    'status'=>200,
                    'data'=>$data,
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

    /**
     * Reset User Password
     * @method: PUT
    */

    public function reset_password_put(){
        $token = $this->get('token');
        $code = $this->verif_Token($token);
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id', 'User ID', 'trim|required|numeric');
        $this->form_validation->set_rules('password', 'Mot de passe', 'trim|password');
        $this->form_validation->set_rules('token', 'Token', 'trim|required');
        $this->form_validation->set_rules('login', 'Login', 'trim|required');
        
        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }else{
            if (!emtpy($code)) {
                $data = $this->input->post();
                $data['id'] = $this->input->post('id',TRUE);
                $data['modified'] = date('Y-m-d\TH:i:s.u');
                $data['password'] = $this->encrypt_pwd($this->input->post('password'));
                $data['token'] = $code; 

                $outpout = $this->UserModel->update_set_password_user($data);
                if ($outpout>0 AND !empty($outpout)) {
                    $message = [
                        'status'=>201,
                        'message'=>"Mot de passe modifié avec succes!"
                    ];

                    $this->response($message, REST_Controller::HTTP_CREATED);
                    
                } else {
                    $message = [
                        'status'=>400,
                        'message'=>"Une erreur est survenue lors de l'enregistrement!"
                    ];

                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $message = [
                    'status'=>400,
                    'message'=>"Token de recupération invalide !"
                ];

                $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * Send password instruction 
     * @method: GET
     */

    public function send_code_get(){
        $key = $this->config->item('PHONEPLUS_jwt_key');
        $data = '';
        $user = $this->get('user');
        if (!empty($user)) {
            $row = $this->UserModel->user_check_email_or_mobile($user);
            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $data = $row;
                $token = array();
                $users = array();
                $confirm_code = $this->code_confirm();

                $date = new DateTime();
                $token['compte'] = $user;
                $token['code'] = $confirm_code;
                $token['iat'] = $date->getTimestamp();
                $token['exp'] = $date->getTimestamp() + 60*60*24;
                $token['time'] = time();
                $token['sub'] = $data->login;

                $code = JWT::encode($token,$key);

                $users['token'] = $code;
                $users['reset_code'] = $confirm_code;
                $users['modified'] = date('Y-m-d\TH:i:s.u');

                $outpout = $this->UserModel->update_user_by_email($user,$users);
                if ($outpout>0 AND !empty($outpout)) {
                    $this->mail_to_send($user,$confirm_code,'Code de recupération');
                    $message = [
                        'status'=>200,
                        'message'=>"Code de récupération envoyé avec succes!",
                        'response'=>$confirm_code
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                        
                } else {
                    $message = [
                        'status'=>400,
                        'message'=>"Une erreur est survenue lors de l'enregistrement!"
                    ];

                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
                }
            }

        } else {
            $this->set_response([
                'status'=>400,
                'message'=>'Parametre manquant !'
            ],
                REST_Controller::HTTP_BAD_REQUEST
            );
            return;
        }
    }

    public function mail_to_send($email,$contenu,$subjet){

        $this->load->config('email');
        $this->load->library('email');

        $from = $this->config->item('smtp_user');
        $to = $email;
        $subject = $subjet;
        $message = $contenu;

        $this->email->initialize($this->config->config);
        $this->email->set_newline("\r\n");
        $this->email->from($from, 'PHONEPLUS CI');
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);

        if ($this->email->send()) {
            echo 'Your Email has successfully been sent.';
        } else {
            show_error($this->email->print_debugger());
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
    
    public function code_confirm(){
        $code = '';
		for($i=0; $i<6; $i++){
			$chiffre = mt_rand(0,9);
			$code.= $chiffre;
		}
		return $code;
    }
    
    public function verif_Token($token){
        $key = $this->config->item('PHONEPLUS_jwt_key');
        if (!empty($token)) {
            $verif_token = JWT::decode($token,$key);
            $exp = $verif_token['exp'];
            $time_now = time();
            if ($exp>$time_now) {
                return $verif_token['code'];
            } else {
                return '';
            }
        } else {
            return '';
        }
            
    }

    
    
}