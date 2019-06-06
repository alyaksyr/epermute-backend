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
class SousCategories extends REST_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('sousCategorie_model','SousCategorieModel');

    }

    /**
     * Get Categorie Article 
     * @method: GET
     */
    public function index_get($param='')
    {
        $sous_categorie= array();
        $this->load->model('categorie_model','CategorieModel');

        if (empty($param)) {
            
            foreach ($this->SousCategorieModel->all_sous_categorie() as $row)
            {
                $data['id'] = $row['id'];
                $data['code'] = $row['code'];
                $data['libelle'] = $row['libelle'];
                $data['created'] = $row['created_at'];
                $data['updated'] = $row['updated_at'];
                $data['infos'] = $row['infos'];
                $data['categorie'] = $this->CategorieModel->categorie($row['id_categorie']);  
                $sous_categorie[] = $data;  
            }     
            if (empty($sous_categorie)) {
                $this->set_response([
                    'status'=>false,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($sous_categorie, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->SousCategorieModel->sous_categorie($param);
            $sous_categorie['id'] = $row->id;
            $sous_categorie['code'] = $row->code;
            $sous_categorie['libelle'] = $row->libelle;
            $sous_categorie['created'] = $row->created_at;
            $sous_categorie['updated'] = $row->updated_at;
            $sous_categorie['infos'] = $row->infos;
            $sous_categorie['categorie']=$this->CategorieModel->categorie($row->id_categorie);

            if (empty($sous_categorie)) {
                $this->set_response([
                    'status'=>false,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($sous_categorie, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response($sous_categorie, REST_Controller::HTTP_OK);
    }

    /**
     * Create New Categorie Article
     * @method: POST
     */
    public function index_post()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('libelle', 'Libelle', 'trim|required');
        $this->form_validation->set_rules('code', 'Code', 'trim|required|is_unique[aqi_pp_categorie_article.code]',
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

            $sous_categorie = $this->input->post();
            $id = $this->SousCategorieModel->create($sous_categorie);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>true,
                    'message'=>"Catégorie Article ajoutée avec succes!"
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
     * Update Categorie Article
     * @method: PUT
     */
    public function index_put()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id', 'Sous Categorie ID', 'trim|required|numeric');
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
            $sous_categorie = $this->input->post();
            $sous_categorie['id'] = $this->input->post('id',TRUE);

            $outpout = $this->SousCategorieModel->update($sous_categorie);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"Catégorie Article Modifiée avec succes!"
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
     * Delete Categorie Article
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>FALSE,
                'message'=>'L\'Id de la catégorie n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $sous_categorie= [
                'id'=>$id
            ];
            $outpout = $this->SousCategorieModel->delete($sous_categorie);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"Catégorie Article supprimée avec succes!"
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