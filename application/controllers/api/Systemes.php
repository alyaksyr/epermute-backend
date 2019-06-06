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
class Systemes extends REST_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('systeme_model', 'SystemeModel');

    }

         /**
     * Get Systeme 
     * @method: GET
     */
    public function index_get($param='')
    {
        $systeme= array();

        if (empty($param)) {
            
            foreach ($this->SystemeModel->all_systeme() as $row)
            {
                $data['id'] = $row['id'];
                $data['code'] = $row['code'];
                $data['libelle'] = $row['libelle'];
                $data['version'] = $row['version'];
                $data['type'] = $row['type'];
                $data['infos'] = $row['infos'];
                $systeme[] = $data;  
            }     
            if (empty($systeme)) {
                $this->set_response([
                    'status'=>false,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($systeme, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->SystemeModel->systeme($param);
            $systeme['id'] = $row->id;
            $systeme['code'] = $row->code;
            $systeme['libelle'] = $row->libelle;
            $systeme['version'] = $row->version;
            $systeme['type'] = $row->type;
            $systeme['infos'] = $row->infos;

            if (empty($systeme)) {
                $this->set_response([
                    'status'=>false,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($systeme, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response($systeme, REST_Controller::HTTP_OK);
    }

    /**
     * Create New Systeme
     * @method: POST
     */
    public function index_post()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('libelle', 'Libelle', 'trim|required');
        $this->form_validation->set_rules('code', 'Code', 'trim|required|is_unique[aqi_pp_systeme.code]',
            array('is_unique'=>'Ce code existe déja !')
        );

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>false,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $systeme = $this->input->post();
            $id = $this->SystemeModel->create($systeme);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>true,
                    'message'=>"Système ajouté avec succes!"
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
     * Update Systeme
     * @method: PUT
     */
    public function index_put()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id', 'Systeme ID', 'trim|required|numeric');
        $this->form_validation->set_rules('code', 'Code', 'trim|required');
        $this->form_validation->set_rules('libelle', 'Libelle', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>false,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $systeme = $this->input->post();
            $systeme['id'] = $this->input->post('id',TRUE);

            $outpout = $this->SystemeModel->update($systeme);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"Systeme Modifié avec succes!"
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
     * Delete Systeme
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>FALSE,
                'message'=>'Cet id n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $systeme= [
                'id'=>$id
            ];
            $outpout = $this->SystemeModel->delete($systeme);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"Système supprimé avec succes!"
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