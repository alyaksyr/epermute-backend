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
class Marques extends REST_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('marque_model', 'MarqueModel');

    }

  /**
     * Get marque 
     * @method: GET
     */
    public function index_get($param='')
    {
        $marque= array();

        if (empty($param)) {
            
            foreach ($this->MarqueModel->all_marque() as $row)
            {
                $data['id'] = $row['id'];
                $data['code'] = $row['code'];
                $data['libelle'] = $row['libelle'];
                $data['infos'] = $row['infos'];
                $marque[] = $data;  
            }     
            if (empty($marque)) {
                $this->set_response([
                    'status'=>false,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($marque, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->MarqueModel->marque($param);
            $marque['id'] = $row->id;
            $marque['code'] = $row->code;
            $marque['libelle'] = $row->libelle;
            $marque['infos'] = $row->infos;

            if (empty($marque)) {
                $this->set_response([
                    'status'=>false,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($marque, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response($marque, REST_Controller::HTTP_OK);
    }

    /**
     * Create New marque
     * @method: POST
     */
    public function index_post()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('libelle', 'Libelle', 'trim|required');
        $this->form_validation->set_rules('code', 'Code', 'trim|required|is_unique[aqi_pp_marque.code]',
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

            $marque = $this->input->post();
            $id = $this->MarqueModel->create($marque);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>true,
                    'message'=>"Marque ajoutée avec succes!"
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
     * Update Marque
     * @method: PUT
     */
    public function index_put()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id', 'Marque ID', 'trim|required|numeric');
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
            $marque = $this->input->post();
            $marque['id'] = $this->input->post('id',TRUE);

            $outpout = $this->MarqueModel->update($marque);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"Marque Modifiée avec succes!"
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
     * Delete Marque
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>FALSE,
                'message'=>'L\'Id de la marque n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $marque= [
                'id'=>$id
            ];
            $outpout = $this->MarqueModel->delete($marque);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"Marque supprimé avec succes!"
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