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
class Reseaux extends MY_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('reseau_model','ReseauModel');

    }

     /**
     * Get Reseau 
     * @method: GET
     */
    public function index_get($param='')
    {
        $reseau= array();
        $msg='';

        if (empty($param)) {
            
            foreach ($this->ReseauModel->all_reseau() as $row)
            {
                $data = $row;
                $reseau[] = $data;  
            }     
            if (empty($reseau)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $this->set_response($reseau, REST_Controller::HTTP_OK);
                $msg = 'Liste des reseaux récupérée avec succès !';
            }
            
        } else {
            $row = $this->ReseauModel->reseau($param);
            
            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $reseau = $row;
                $this->set_response($reseau, REST_Controller::HTTP_OK);
                $msg = 'Reseau récupérée avec succès !';
            }

        }
        $this->set_response(['status'=>200, 'message'=>$msg, 'data'=>$reseau], REST_Controller::HTTP_OK);
    }

    /**
     * Create New reseau
     * @method: POST
     */
    public function index_post()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('reseau_libelle', 'Reseau Libelle', 'trim|required');
        $this->form_validation->set_rules('reseau_code', 'Reseau Code', 'trim|required|is_unique[aqi_pp_reseau.code]',
            array('is_unique'=>'Ce code existe déja !')
        );

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }else{

            $reseau = $this->input->post();
            $id = $this->ReseauModel->create($reseau);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>201,
                    'message'=>"Reseau ajouté avec succes!",
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
     * Update Reseau
     * @method: PUT
     */
    public function index_put()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('reseau_id', 'Reseau ID', 'trim|required|numeric');
        $this->form_validation->set_rules('reseau_code', 'Reseau Code', 'trim|required');
        $this->form_validation->set_rules('reseau_libelle', 'Reseau Libelle', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }else{
            $reseau = $this->input->post();
            $reseau['reseau_id'] = $this->input->post('reseau_id',TRUE);

            $outpout = $this->ReseauModel->update($reseau);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Reseau Modifié avec succes!"
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
     * Delete Marque
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $this->auth();
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>404,
                'message'=>'Cet id n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
            return;
        } else {
            $reseau= [
                'reseau_id'=>$id
            ];
            $outpout = $this->ReseauModel->delete($reseau);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"Reseau supprimé avec succes!"
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