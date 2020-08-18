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
class Paniers extends MY_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->auth();
        $this->load->model('panier_model','PanierModel');

    }

    /**
     * Get Panier 
     * @method: GET
     */
    public function index_get($param='')
    {
        $panier= array();
        $msg='';

        if (empty($param)) {
            
            foreach ($this->PanierModel->all_panier() as $row)
            {
                $data = $row;
                $panier[] = $data;  
            }     
            if (empty($panier)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($panier, REST_Controller::HTTP_OK);
                $msg = 'Liste des paniers récupérée avec succès !';
            }
            
        } else {
            $row = $this->PanierModel->panier($param);

            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $panier = $row;

                $this->set_response($panier, REST_Controller::HTTP_OK);
                $msg = 'Panier récupérée avec succès !';
            }

        }
        $this->set_response($panier, REST_Controller::HTTP_OK);
    }

    /**
     * Create New pays
     * @method: POST
     */
    public function index_post()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id_produit', 'ID Produit', 'trim|required|numeric');
        $this->form_validation->set_rules('id_client', 'ID Client', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $panier = $this->input->post();
            $id = $this->PanierModel->create($panier);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>201,
                    'message'=>"panier ajouté avec succes!"
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

        $this->form_validation->set_rules('id_produit', 'ID Produit', 'trim|required|numeric');
        $this->form_validation->set_rules('id_client', 'ID Client', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $panier = $this->input->post();
            $panier['id'] = $this->input->post('id',TRUE);

            $outpout = $this->PanierModel->update($panier);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"panier Modifié avec succes!"
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
                'message'=>'L\'Id du type de panier n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $panier= [
                'id'=>$id
            ];
            $outpout = $this->PanierModel->delete($panier);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"panier supprimé avec succes!"
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