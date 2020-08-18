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
class Categories extends MY_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('categorie_model','CategorieModel');

    }

    /**
     * Get Type Article
     * @method: GET
     */
    public function index_get($param='')
    {
        $categorie= array();
        $msg ='';

        if (empty($param)) {
            
            foreach ($this->CategorieModel->all_categorie() as $row)
            {
                $data['id'] = (int)$row['cat_id'];
                $data['libelle'] = $row['cat_libelle'];
                $data['created'] = $row['cat_created_at'];
                $data['updated'] = $row['cat_updated_at'];
                $data['infos'] = $row['cat_infos'];
                $data['slug'] = $row['cat_slug'];
                $data['logo'] = $row['cat_image'];
                $categorie[] = $data;  
            }     
            if (empty($categorie)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $this->set_response($categorie, REST_Controller::HTTP_OK);
                $msg ='Liste des catégories d\'articles recupérée avec succès !';
            }
            
        } else {
            $row = $this->CategorieModel->categorie($param);

            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $categorie['id'] = (int)$row->cat_id;
                $categorie['libelle'] = $row->cat_libelle;
                $categorie['created'] = $row->cat_created_at;
                $categorie['updated'] = $row->cat_updated_at;
                $categorie['infos'] = $row->cat_infos;
                $categorie['slug'] = $row->cat_slug;
                $categorie['logo'] = $row->cat_image;
                $this->set_response($categorie, REST_Controller::HTTP_OK);
                $msg ='Catégorie d\'articles recupérée avec succès !';
            }

        }
        $this->set_response(['status'=>200, 'message'=>$msg, 'data'=>$categorie], REST_Controller::HTTP_OK);
    }

    /**
     * Create New Type Article
     * @method: POST
     */
    public function index_post()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('cat_libelle', 'Libelle', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $categorie = $this->input->post();
            $id = $this->CategorieModel->create($categorie);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>201,
                    'message'=>"Catégorie produit ajoutée avec succes!",
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
     * Update Categorie produit
     * @method: PUT
     */
    public function index_put()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('cat_id', 'Categorie ID', 'trim|required|numeric');
        $this->form_validation->set_rules('cat_libelle', 'Libelle', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $categorie = $this->input->post();
            $categorie['cat_id'] = $this->input->post('cat_id',TRUE);

            $outpout = $this->CategorieModel->update($categorie);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Categorie Modifiée avec succes!"
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
     * Delete Type Article
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $this->auth();
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>404,
                'message'=>'L\'Id  n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $categorie= [
                'cat_id'=>$id
            ];
            $outpout = $this->CategorieModel->delete($categorie);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"Catégorie supprimée avec succes!"
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