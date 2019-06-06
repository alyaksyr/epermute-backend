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
class Ecrans extends REST_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('ecran_model', 'EcranModel');

    }

    /**
     * Get Camera 
     * @method: GET
     */
    public function index_get($param='')
    {
        if (empty($param)) {
            $ecran= array();
            foreach ($this->EcranModel->all_ecran() as $row)
            {
                $data['id'] = $row['id'];
                $data['code'] = $row['code'];
                $data['taille'] = $row['taille'].' '.$row['unite'];
                $data['infos'] = $row['infos'];
                $ecran[] = $data;  
            }     
            if (empty($ecran)) {
                $this->set_response([
                    'status'=>false,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($ecran, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->EcranModel->ecran($param);
            $ecran['id'] = $row->id;
            $ecran['code'] = $row->code;
            $ecran['taille'] = $row->taille;
            $ecran['unite'] = $row->unite;
            $ecran['infos'] = $row->infos;

            if (empty($ecran)) {
                $this->set_response([
                    'status'=>false,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($ecran, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response($ecran, REST_Controller::HTTP_OK);
    }

    /**
     * Create New pays
     * @method: POST
     */
    public function index_post()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('code', 'Code', 'trim|required|is_unique[aqi_pp_ecran.code]',
            array('is_unique'=>'Ce code existe déja !')
        );
        $this->form_validation->set_rules('taille', 'Taille', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>false,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $ecran = $this->input->post();
            $id = $this->EcranModel->create($ecran);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>true,
                    'message'=>"Ecran ajouté avec succes!"
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
     * Update Ville
     * @method: PUT
     */
    public function index_put()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id', 'Ecran ID', 'trim|required|numeric');
        $this->form_validation->set_rules('code', 'Code', 'trim|required');
        $this->form_validation->set_rules('taille', 'Taille', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>false,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $ecran = $this->input->post();
            $ecran['id'] = $this->input->post('id',TRUE);

            $outpout = $this->EcranModel->update($ecran);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"Ecran Modifié avec succes!"
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
     * Delete Pays
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
            $ecran= [
                'id'=>$id
            ];
            $outpout = $this->EcranModel->delete($ecran);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"Ecran supprimée avec succes!"
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