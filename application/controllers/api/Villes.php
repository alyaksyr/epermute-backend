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
class Villes extends MY_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('ville_model','VilleModel');
        $this->load->model('pays_model','PaysModel');

    }

    /**
     * Get ville 
     * @method: GET
     */
    public function index_get($param='')
    {
        $ville= array();

        if (empty($param)) {
            
            foreach ($this->VilleModel->all_villes() as $row)
            {
                $data['id'] = (int)$row->ville_id;
                $data['code'] = $row->ville_code;
                $data['libelle'] = $row->ville_libelle;
                $data['pays']=$this->get_pays($row->id_pays); 
                $ville[] = $data;  
            }     
            if (empty($ville)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $this->set_response($ville, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->VilleModel->ville($param);

            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $ville['id'] = (int)$row->ville_id;
                $ville['code'] = $row->ville_code;
                $ville['libelle'] = $row->ville_libelle;
                $ville['pays']=$this->get_pays($row->id_pays);
                $this->set_response($ville, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response(['status'=>200,'message'=>'Succès','data'=>$ville], REST_Controller::HTTP_OK);
    }

    /**
     * Get ville joined Pays
     * @method: GET
     */
    public function ville_get($param='')
    {
        $ville= array();

        if (empty($param)) {
            
            foreach ($this->VilleModel->all_ville_pays() as $row)
            {
                $data = $row;
                $ville[] = $data;  
            }     
            if (empty($ville)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($ville, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->VilleModel->ville_pays($param);
            
            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $ville = $row;
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
        $this->auth();
        $_POST = $this->security->xss_clean($_POST);

        $this->form_validation->set_rules('ville_libelle', 'Ville Libelle', 'trim|required');
        $this->form_validation->set_rules('id_pays', 'Pays ID', 'trim|required|numeric');
        $this->form_validation->set_rules('ville_code', 'Ville Code', 'trim|required|is_unique[aqi_pp_ville.ville_code]',
            array('is_unique'=>'Ce code de ville existe déja !')
        );

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $ville = $this->input->post();
            $id = $this->VilleModel->create($ville);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>201,
                    'message'=>"Ville ajoutée avec succes!",
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

        $this->form_validation->set_rules('ville_id', 'Ville ID', 'trim|required|numeric');
        $this->form_validation->set_rules('ville_code', 'Ville Code', 'trim|required');
        $this->form_validation->set_rules('id_pays', 'Pays ID', 'trim|required|numeric');
        $this->form_validation->set_rules('ville_libelle', 'Ville Libelle', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $ville = $this->input->post();
            $ville['ville_id'] = $this->input->post('ville_id',TRUE);

            $outpout = $this->VilleModel->update($ville);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Ville Modifiée avec succes!"
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
     * Delete Ville
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $this->auth();
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>404,
                'message'=>'L\'Id de la ville n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $ville= [
                'ville_id'=>$id
            ];
            $outpout = $this->VilleModel->delete($ville);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"Ville supprimée avec succes!"
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

    public function get_pays($id){
        $row = $this->PaysModel->pays($id);
        $pays['id'] = (int)$row->pays_id;
        $pays['code'] = $row->pays_code;
        $pays['libelle'] = $row->pays_libelle;

        return $pays;
    }

}