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
class Commandes extends MY_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->auth();
        $this->load->model('commande_model','CommandeModel');

    }

    /**
     * Get commande 
     * @method: GET
     */
    public function index_get($param='')
    {
        if (empty($param)) {

            $commande = array();
            $msg='';

            foreach ($this->CommandeModel->all_commande() as $row)
            {
                $data = $row;
                $commande[] = $data;  
            }     
            if (empty($commande)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $this->set_response($commande, REST_Controller::HTTP_OK);
                $msg='Liste des commandes récupérée avec succès !';
            }
            
        } else {
            $row = $this->CommandeModel->commande($param);

            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $commande = $row;
                $this->set_response($commande, REST_Controller::HTTP_OK);
                $msg='Commande récupérée avec succès !';
            }

        }
        $this->set_response(['status'=>200, 'message'=>$msg, 'data'=>$commande], REST_Controller::HTTP_OK);
    }

    /**
     * Create New pays
     * @method: POST
     */
    public function index_post()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id_client', 'ID Client', 'trim|required|numeric');
        $this->form_validation->set_rules('code', 'Code Commande', 'trim|required|is_natural_no_zero|is_unique[aqi_pp_commande.code]', 
        array('is_unique'=>'Ce numero de commande existe déja !'));
        $this->form_validation->set_rules('qte_article', 'QTE Article', 'trim|required|numeric|is_natural_no_zero');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $commande = $this->input->post();
            $id = $this->CommandeModel->create($commande);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>201,
                    'message'=>"Commande ajoutée avec succes!",
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
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id_client', 'ID Client', 'trim|required|numeric');
        $this->form_validation->set_rules('code', 'Code Commande', 'trim|required|is_natural_no_zero', 
        array('is_unique'=>'Ce numero de commande existe déja !'));
        $this->form_validation->set_rules('qte_article', 'QTE Article', 'trim|required|numeric|is_natural_no_zero');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $commande = $this->input->post();
            $commande['id'] = $this->input->post('id',TRUE);

            $outpout = $this->CommandeModel->update($commande);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Commande Modifiée avec succes!"
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
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>404,
                'message'=>'L\'Id du type de commande n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $commande= [
                'id'=>$id
            ];
            $outpout = $this->CommandeModel->delete($commande);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"commande supprimé avec succes!"
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