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
class Modeles extends MY_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('modele_model','ModeleModel');
        $this->load->model('marque_model','MarqueModel');

    }

    /**
     * Get Modeles 
     * @method: GET
     */
    public function index_get($param='')
    {
        $modele= array();
        $msg='';

        if (empty($param)) {
            
            foreach ($this->ModeleModel->all_modele() as $row)
            {
                $data['modele_id'] = (int)$row['modele_id'];
                $data['modele_code'] = $row['modele_code'];
                $data['modele_libelle'] = $row['modele_libelle'];
                $data['marque'] = $this->get_marque($row['id_marque']); 
                $data['modele_infos'] = $row['modele_infos']; 
                $modele[] = $data;  
            }     
            if (empty($modele)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($modele, REST_Controller::HTTP_OK);
                $msg = 'Liste des modèles récupérée avec succès !';
            }
            
        } else {
            $row = $this->ModeleModel->modele($param);
            
            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $modele['modele_id'] = (int)$row->modele_id;
                $modele['modele_code'] = $row->modele_code;
                $modele['modele_libelle'] = $row->modele_libelle;
                $modele['marque']=$this->get_marque($row->id_marque);
                $modele['modele_infos'] = $row->modele_infos;

                $this->set_response($modele, REST_Controller::HTTP_OK);
                $msg = 'Modèle récupéré avec succès !';
            }

        }
        $this->set_response(['status'=>200, 'message'=>$msg, 'data'=>$modele], REST_Controller::HTTP_OK);
    }

    /**
     * Create New Modele
     * @method: POST
     */
    public function index_post()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('modele_libelle', 'Modele Libelle', 'trim|required');
        $this->form_validation->set_rules('id_marque', 'Marque ID', 'trim|required|numeric');
        $this->form_validation->set_rules('modele_code', 'Modele Code', 'trim|required|is_unique[aqi_pp_modele.modele_code]',
            array('is_unique'=>'Ce code de modèle existe déja !')
        );

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $modele = $this->input->post();
            $id = $this->ModeleModel->create($modele);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>201,
                    'message'=>"Modèle ajouté avec succes!",
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
     * Update Modele
     * @method: PUT
     */
    public function index_put()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('modele_id', 'Modele ID', 'trim|required|numeric');
        $this->form_validation->set_rules('modele_code', 'Code', 'trim|required');
        $this->form_validation->set_rules('modele_libelle', 'Libelle', 'trim|required');
        $this->form_validation->set_rules('id_marque', 'Marque ID', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $modele = $this->input->post();
            $modele['modele_id'] = $this->input->post('modele_id',TRUE);

            $outpout = $this->ModeleModel->update($modele);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Modèle Modifié avec succes!"
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
     * Delete Modele
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $this->auth();
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>404,
                'message'=>'L\'Id du modèle n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $modele= [
                'modele_id'=>$id
            ];
            $outpout = $this->ModeleModel->delete($modele);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"Modèle supprimé avec succes!"
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

    public function get_marque($id){
        $row = $this->MarqueModel->marque($id);
        $marque['marque_id'] = (int)$row->marque_id;
        $marque['marque_code'] = $row->marque_code;
        $marque['marque_libelle'] = $row->marque_libelle;
        $marque['marque_infos'] = $row->marque_infos;

        return $marque;
    }
}