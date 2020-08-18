<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Phoneplus\Libraries\REST_Controller;
require APPPATH .'libraries/Format.php';

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
class Memoires extends MY_Controller {
    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('memoire_model','MemoireModel');

    }

    /**
     * Get Memoire 
     * @method: GET
     */
    public function index_get($param='')
    {
        $memoire= array();
        $msg='';
        if (empty($param)) {
            
            foreach ($this->MemoireModel->all_memoire() as $row)
            {
                $data = $row;
                $memoire[] = $data;  
            }     
            if (empty($memoire)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($memoire, REST_Controller::HTTP_OK);
                $msg = 'Liste des memoires récupérée avec succès !';
            }
            
        } else {
            $row = $this->MemoireModel->memoire($param);
            
            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $memoire = $row;
                $this->set_response($memoire, REST_Controller::HTTP_OK);
                $msg = 'Memoire récupérée avec succès !';
            }

        }
        $this->set_response(['status'=>200, 'message'=>$msg, 'data'=>$memoire], REST_Controller::HTTP_OK);
    }

    /**
     * Create New memoire
     * @method: POST
     */
    public function index_post()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('memoire_code', 'Code', 'trim|required|is_unique[aqi_pp_memoire.memoire_code]',
            array('is_unique'=>'Ce code existe déja !')
        );
        $this->form_validation->set_rules('memoire_capacite', 'Capacite', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $memoire = $this->input->post();
            $id = $this->MemoireModel->create($memoire);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>201,
                    'message'=>"Mémoire ajoutée avec succes!",
                    'response'=>base_url().'/'.$id
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
     * Update Memoire
     * @method: PUT
     */
    public function index_put()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('memoire_id', 'Memoire ID', 'trim|required|numeric');
        $this->form_validation->set_rules('memoire_code', 'Code', 'trim|required');
        $this->form_validation->set_rules('memoire_capacite', 'Capacite', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $memoire = $this->input->post();
            $memoire['memoire_id'] = $this->input->post('memoire_id',TRUE);

            $outpout = $this->MemoireModel->update($memoire);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Memoire Modifiée avec succes!"
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
     * Delete Memoire
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $this->auth();
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>404,
                'message'=>'Cet Id n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $memoire= [
                'memoire_id'=>$id
            ];
            $outpout = $this->MemoireModel->delete($memoire);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"Memoire supprimée avec succes!"
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
    }

}