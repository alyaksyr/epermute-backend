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
class Champs extends MY_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('champMeta_model','ChampMetaModel');
        $this->load->model('categorie_model','CategorieModel');

    }

    /**
     * Get champ 
     * @method: GET
     */
    public function index_get($param='')
    {
        $champ= array();
        $msg ='';

        if (empty($param)) {
            
            foreach ($this->ChampMetaModel->all_champ() as $row)
            {
                $data['id'] = $row->id;
                $data['code'] = $row->code;
                $data['libelle'] = $row->libelle;
                $data['required'] = $row->is_required;
                $data['input'] = $row->type_input;
                $data['value'] = $row->default_value;
                $data['categorie'] = $this->CategorieModel->categorie((int)$row->id_categorie);
                $data['order'] = $row->default_order;
                $data['type'] = $row->type_champ;
                
                $champ[] = $data;  
            }     
            if (empty($champ)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $this->set_response($champ, REST_Controller::HTTP_OK);
                $msg='Liste des champ récupérée avec succès !';
            }
            
        } else {
            $row = $this->ChampMetaModel->champ($param);

            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $champ['id'] = $row->id;
                $champ['code'] = $row->code;
                $champ['libelle'] = $row->libelle;
                $champ['required'] = $row->is_required;
                $champ['input'] = $row->type_input;
                $champ['value'] = $row->default_value;
                $champ['categorie'] = $this->CategorieModel->categorie((int)$row->id_categorie);
                $champ['order'] = $row->default_order;
                $champ['type'] = $row->type_champ;

                $this->set_response($champ, REST_Controller::HTTP_OK);

                $msg='Champ récupérée avec succès !';
            }

        }
        $this->set_response(['status'=>200, 'message'=>$msg, 'data'=>$champ], REST_Controller::HTTP_OK);
    }

    /**
     * Create New champ
     * @method: POST
     */
    public function index_post()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('libelle', 'libelle', 'trim|required');
        $this->form_validation->set_rules('code', 'Code', 'trim|required|is_unique[aqi_pp_champ_meta.code]',
            array('is_unique'=>'Ce code existe déja !')
        );
        $this->form_validation->set_rules('libelle', 'Libelle', 'trim|required|is_unique[aqi_pp_champ_meta.libelle]',
            array('is_unique'=>'Ce libelle existe déja !')
        );

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $champ = $this->input->post();
            $id = $this->ChampMetaModel->create($champ);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>201,
                    'message'=>"Champ ajouté avec succes!",
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

        $this->form_validation->set_rules('id', 'Pays ID', 'trim|required|numeric');
        $this->form_validation->set_rules('code', 'Code', 'trim|required');
        $this->form_validation->set_rules('libelle', 'Libelle', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $champ = $this->input->post();
            $champ['id'] = $this->input->post('id',TRUE);

            $outpout = $this->ChampMetaModel->update($champ);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Champ Modifié avec succes!"
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
                'message'=>'L\'Id du champ n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $champ= [
                'id'=>$id
            ];
            $outpout = $this->ChampMetaModel->delete($champ);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"Champ supprimé avec succes!"
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