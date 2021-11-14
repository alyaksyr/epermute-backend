<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Epermute\Libraries\REST_Controller;
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
class Pays extends MY_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('pays_model','PaysModel');

    }

    /**
     * Get pays 
     * @method: GET
     */
    public function index_get($param='')
    {
        $pays= array();

        if (empty($param)) {
            
            foreach ($this->PaysModel->all_pays() as $row)
            {
                $data = $row;
                $pays[] = $data;  
            }     
            if (empty($pays)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found,
                    'data'=>$pays
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $this->set_response($pays, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->PaysModel->pays($param);

            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found,
                    'data'=>$row
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $pays = $row;
                $this->set_response($pays, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response(['status'=>200,'message'=>'Requête exécutée avec succès !','data'=>$pays], REST_Controller::HTTP_OK);
    }

    /**
     * Create New pays
     * @method: POST
     */
    public function index_post()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('pays_libelle', 'Pays Libelle', 'trim|required');
        $this->form_validation->set_rules('pays_code', 'Pays Code', 'trim|required|is_unique[aqi_pp_pays.pays_code]',
            array('is_unique'=>'Ce code de pays existe déja !')
        );
        $this->form_validation->set_rules('pays_indicatif', 'Pays Code Indicatif', 'trim|required|is_unique[aqi_pp_pays.pays_indicatif]',
            array('is_unique'=>'Ce code indicatif de pays existe déja !')
        );

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $pays = $this->input->post();
            $id = $this->PaysModel->create($pays);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>201,
                    'message'=>"Pays ajouté avec succes!",
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
     * Update Ville
     * @method: PUT
     */
    public function index_put()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('pays_id', 'Pays ID', 'trim|required|numeric');
        $this->form_validation->set_rules('pays_code', 'Code', 'trim|required');
        $this->form_validation->set_rules('pays_indicatif', 'Code Indicatif', 'trim|required');
        $this->form_validation->set_rules('pays_libelle', 'Pays Libelle', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $pays = $this->input->post();
            $pays['pays_id'] = $this->input->post('pays_id',TRUE);

            $outpout = $this->PaysModel->update($pays);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Pays Modifié avec succes!"
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
     * Delete Pays
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $this->auth();
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>404,
                'message'=>'L\'Id du pays n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $pays= [
                'pays_id'=>$id
            ];
            $outpout = $this->PaysModel->delete($pays);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"Pays supprimé avec succes!"
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