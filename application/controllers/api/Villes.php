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
class Villes extends REST_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('ville_model','VilleModel');

    }

    /**
     * Get ville 
     * @method: GET
     */
    public function index_get($param='')
    {
        $ville= array();
        $this->load->model('pays_model','PaysModel');

        if (empty($param)) {
            
            foreach ($this->VilleModel->all_villes() as $row)
            {
                $data['id'] = $row->id;
                $data['code'] = $row->code;
                $data['libelle'] = $row->libelle;
                $data['pays']=$this->PaysModel->pays($row->id_pays); 
                $ville[] = $data;  
            }     
            if (empty($ville)) {
                $this->set_response([
                    'status'=>false,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($ville, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->VilleModel->ville($param);
            $ville['id'] = $row->id;
            $ville['code'] = $row->code;
            $ville['libelle'] = $row->libelle;
            $ville['pays']=$this->PaysModel->pays($row->id_pays);

            if (empty($ville)) {
                $this->set_response([
                    'status'=>false,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($ville, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response($ville, REST_Controller::HTTP_OK);
    }

    /**
     * Create New Ville
     * @method: POST
     */
    public function index_post()
    {
        $_POST = $this->security->xss_clean($_POST);

        $this->form_validation->set_rules('libelle', 'libelle', 'trim|required');
        $this->form_validation->set_rules('code', 'Code', 'trim|required|is_unique[aqi_pp_ville.code]',
            array('is_unique'=>'Ce code de ville existe déja !')
        );

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>false,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $ville = $this->input->post();
            $id = $this->VilleModel->create($ville);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>true,
                    'message'=>"Ville ajoutée avec succes!"
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

        $this->form_validation->set_rules('id', 'Ville ID', 'trim|required|numeric');
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
            $ville = $this->input->post();
            $ville['id'] = $this->input->post('id',TRUE);

            $outpout = $this->VilleModel->update($ville);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"Ville Modifiée avec succes!"
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
     * Delete Ville
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>FALSE,
                'message'=>'L\'Id de la ville n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $ville= [
                'id'=>$id
            ];
            $outpout = $this->VilleModel->delete($ville);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"Ville supprimée avec succes!"
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