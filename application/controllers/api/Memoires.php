<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Phoneplus\Libraries\REST_Controller;
require APPPATH .'libraries/REST_Controller.php';
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
class Memoires extends REST_Controller {
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
        if (empty($param)) {
            $memoire= array();
            foreach ($this->MemoireModel->all_memoire() as $row)
            {
                $data['id'] = $row['id'];
                $data['code'] = $row['code'];
                $data['type'] = $row['type'];
                $data['capacite'] = $row['capacite'].' '.$row['unite'];
                $data['infos'] = $row['infos'];
                $memoire[] = $data;  
            }     
            if (empty($memoire)) {
                $this->set_response([
                    'status'=>false,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($memoire, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->MemoireModel->memoire($param);
            $memoire['id'] = $row->id;
            $memoire['code'] = $row->code;
            $memoire['type'] = $row->type;
            $memoire['capacite'] = $row->capacite;
            $memoire['unite'] = $row->unite;
            $memoire['infos'] = $row->infos;

            if (empty($memoire)) {
                $this->set_response([
                    'status'=>false,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($memoire, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response($memoire, REST_Controller::HTTP_OK);
    }

    /**
     * Create New memoire
     * @method: POST
     */
    public function index_post()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('code', 'Code', 'trim|required|is_unique[aqi_pp_memoire.code]',
            array('is_unique'=>'Ce code existe déja !')
        );
        $this->form_validation->set_rules('capacite', 'Capacite', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>false,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $memoire = $this->input->post();
            $id = $this->MemoireModel->create($memoire);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>true,
                    'message'=>"Mémoire ajoutée avec succes!"
                ];
                $this->response($message, REST_Controller::HTTP_CREATED);
                
            } else {
                $message = [
                    'status'=>false,
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
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id', 'Memoire ID', 'trim|required|numeric');
        $this->form_validation->set_rules('code', 'Code', 'trim|required');
        $this->form_validation->set_rules('capacite', 'Capacite', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>false,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $memoire = $this->input->post();
            $memoire['id'] = $this->input->post('id',TRUE);

            $outpout = $this->MemoireModel->update($memoire);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"Memoire Modifiée avec succes!"
                ];

                $this->response($message, REST_Controller::HTTP_CREATED);
                
            } else {
                $message = [
                    'status'=>false,
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
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>FALSE,
                'message'=>'Cet Id n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $memoire= [
                'id'=>$id
            ];
            $outpout = $this->MemoireModel->delete($memoire);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"Memoire supprimée avec succes!"
                ];

                $this->response($message, REST_Controller::HTTP_OK);
                
            } else {
                $message = [
                    'status'=>false,
                    'message'=>"Une erreur est survenue lors de l'enregistrement!"
                ];

                $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }

}