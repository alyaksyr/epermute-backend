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
class Champs extends REST_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('champMeta_model','ChampMetaModel');

    }

    /**
     * Get champ 
     * @method: GET
     */
    public function index_get($param='')
    {
        $champ= array();

        if (empty($param)) {
            
            foreach ($this->ChampMetaModel->all_champ() as $row)
            {
                $data['id'] = $row['id'];
                $data['code'] = $row['code'];
                $data['libelle'] = $row['libelle'];
                $champ[] = $data;  
            }     
            if (empty($champ)) {
                $this->set_response([
                    'status'=>false,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($champ, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->ChampMetaModel->champ($param);
            $champ['id'] = $row->id;
            $champ['code'] = $row->code;
            $champ['libelle'] = $row->libelle;

            if (empty($champ)) {
                $this->set_response([
                    'status'=>false,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($champ, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response($champ, REST_Controller::HTTP_OK);
    }

    /**
     * Create New champ
     * @method: POST
     */
    public function index_post()
    {
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
                'status'=>false,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $champ = $this->input->post();
            $id = $this->ChampMetaModel->create($champ);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>true,
                    'message'=>"Champ ajouté avec succes!"
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

        $this->form_validation->set_rules('id', 'Pays ID', 'trim|required|numeric');
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
            $champ = $this->input->post();
            $champ['id'] = $this->input->post('id',TRUE);

            $outpout = $this->ChampMetaModel->update($champ);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"Champ Modifié avec succes!"
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
                    'status'=>true,
                    'message'=>"Champ supprimé avec succes!"
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