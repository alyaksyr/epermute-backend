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
class Etats extends REST_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('etatArticle_model','EtatArticleModel');

    }

    /**
     * Get Etat Article
     * @method: GET
     */
    public function index_get($param='')
    {
        $etat= array();

        if (empty($param)) {
            
            foreach ($this->EtatArticleModel->all_etat() as $row)
            {
                $data['id'] = $row['id'];
                $data['code'] = $row['code'];
                $data['libelle'] = $row['libelle'];
                $etat[] = $data;  
            }     
            if (empty($etat)) {
                $this->set_response([
                    'status'=>false,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($etat, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->EtatArticleModel->etat($param);
            $etat['id'] = $row->id;
            $etat['code'] = $row->code;
            $etat['libelle'] = $row->libelle;

            if (empty($etat)) {
                $this->set_response([
                    'status'=>false,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($etat, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response($etat, REST_Controller::HTTP_OK);
    }

    /**
     * Create New Etat Article
     * @method: POST
     */
    public function index_post()
    {
        $_POST = $this->security->xss_clean($_POST);

        $this->form_validation->set_rules('libelle', 'Libelle', 'trim|required');
        $this->form_validation->set_rules('code', 'Code', 'trim|required|is_unique[aqi_pp_etat_article.code]',
            array('is_unique'=>'Ce code existe déja !')
        );

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>false,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $etat = $this->input->post();
            $id = $this->EtatArticleModel->create($etat);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>true,
                    'message'=>"Etat Article ajouté avec succes!"
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
     * Update Etat Article
     * @method: PUT
     */
    public function index_put()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id', 'Etat ID', 'trim|required|numeric');
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
            $etat = $this->input->post();
            $etat['id'] = $this->input->post('id',TRUE);

            $outpout = $this->EtatArticleModel->update($etat);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"Etat Article Modifié avec succes!"
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
     * Delete Etat Article
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>FALSE,
                'message'=>'L\'Id  n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $etat= [
                'id'=>$id
            ];
            $outpout = $this->EtatArticleModel->delete($etat);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"Etat Article supprimé avec succes!"
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