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
class Marques extends MY_Controller {

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
        $msg ='';

        if (empty($param)) {
            
            foreach ($this->MarqueModel->all_marque() as $row)
            {
                $data['marque_id'] = (int)$row['marque_id'];
                $data['marque_code'] = $row['marque_code'];
                $data['marque_libelle'] = $row['marque_libelle'];
                $data['marque_infos'] = $row['marque_infos'];
                $marque[] = $data;  
            }     
            if (empty($marque)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $this->set_response($marque, REST_Controller::HTTP_OK);
                $msg = 'Liste des marques récupérée avec succès !';
            }
            
        } else {
            $row = $this->MarqueModel->marque($param);
            
            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $marque['marque_id'] = (int)$row->marque_id;
                $marque['marque_code'] = $row->marque_code;
                $marque['marque_libelle'] = $row->marque_libelle;
                $marque['marque_infos'] = $row->marque_infos;

                $this->set_response($marque, REST_Controller::HTTP_OK);
                $msg = 'Marque récupérée avec succès !';
            }

        }
        $this->set_response(['status'=>200, 'message'=>$msg, 'data'=>$marque], REST_Controller::HTTP_OK);
    }

    /**
     * Create New marque
     * @method: POST
     */
    public function index_post()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('marque_libelle', 'Libelle Marque', 'trim|required');
        $this->form_validation->set_rules('marque_code', 'Code Marque', 'trim|required|is_unique[aqi_pp_marque.marque_code]',
            array('is_unique'=>'Ce code existe déja !')
        );

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $marque = $this->input->post();
            $id = $this->MarqueModel->create($marque);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>201,
                    'message'=>"Marque ajoutée avec succes!",
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

    public function marque_modele_get($param){
        $marque = array();
        if (!empty($param)) {
           $row = $this->MarqueModel->marque_modele($param);
           $this->set_response($row, REST_Controller::HTTP_OK);
        } else {
            $this->set_response([
                'status'=>404,
                'message'=>$this->msg_not_found
            ],
                REST_Controller::HTTP_NOT_FOUND
            );
            return;
        }
        $this->set_response(['status'=>200, 'message'=>'Marque récupérée avec succès !', 'data'=>$row], REST_Controller::HTTP_OK);
    }

    /**
     * Update Marque
     * @method: PUT
     */
    public function index_put()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('marque_id', 'Marque ID', 'trim|required|numeric');
        $this->form_validation->set_rules('marque_code', 'Code Marque', 'trim|required');
        $this->form_validation->set_rules('marque_libelle', 'Libelle Marque ', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $marque = $this->input->post();
            $marque['marque_id'] = $this->input->post('marque_id',TRUE);

            $outpout = $this->MarqueModel->update($marque);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Marque Modifiée avec succes!"
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
                'message'=>'L\'Id de la marque n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $marque= [
                'marque_id'=>$id
            ];
            $outpout = $this->MarqueModel->delete($marque);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"Marque supprimé avec succes!"
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