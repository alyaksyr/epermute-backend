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
class SousCategories extends MY_Controller {

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
        $msg = '';
        $this->load->model('categorie_model','CategorieModel');

        if (empty($param)) {
            
            foreach ($this->SousCategorieModel->all_sous_categorie() as $row)
            {
                $data['id'] = $row['scat_id'];
                $data['libelle'] = $row['scat_libelle'];
                $data['created'] = $row['scat_created_at'];
                $data['updated'] = $row['scat_updated_at'];
                $data['infos'] = $row['scat_infos'];
                $data['slug'] = $row['scat_slug'];
                $data['logo'] = $row['scat_image'];
                $data['categorie'] = $this->CategorieModel->categorie($row['scat_id_categorie']);  
                $sous_categorie[] = $data;  
            }     
            if (empty($sous_categorie)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $this->set_response($sous_categorie, REST_Controller::HTTP_OK);
                $msg = 'Liste des sous catégories récupérée avec succès !';
            }
            
        } else {
            $row = $this->SousCategorieModel->sous_categorie($param);
            
            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $sous_categorie['id'] = $row->scat_id;
                $sous_categorie['libelle'] = $row->scat_libelle;
                $sous_categorie['created'] = $row->scat_created_at;
                $sous_categorie['updated'] = $row->scat_updated_at;
                $sous_categorie['infos'] = $row->scat_infos;
                $sous_categorie['slug'] = $row->scat_slug;
                $sous_categorie['logo'] = $row->scat_image;
                $sous_categorie['categorie']=$this->CategorieModel->categorie($row->scat_id_categorie);

                $this->set_response($sous_categorie, REST_Controller::HTTP_OK);
                $msg = 'Sous catégorie récupérée avec succès !';
            }

        }
        $this->set_response(['status'=>200, 'message'=>$msg, 'data'=>$sous_categorie], REST_Controller::HTTP_OK);
    }

    /**
     * Create New Categorie Article
     * @method: POST
     */
    public function index_post()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('scat_libelle', 'Libelle', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }else{

            $sous_categorie = $this->input->post();
            $id = $this->SousCategorieModel->create($sous_categorie);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>201,
                    'message'=>"Catégorie Article ajoutée avec succes!",
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
     * Update Categorie Article
     * @method: PUT
     */
    public function index_put()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('scat_id', 'Sous Categorie ID', 'trim|required|numeric');
        $this->form_validation->set_rules('scat_libelle', 'Libelle', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }else{
            $sous_categorie = $this->input->post();
            $sous_categorie['scat_id'] = $this->input->post('scat_id',TRUE);

            $outpout = $this->SousCategorieModel->update($sous_categorie);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Catégorie Article Modifiée avec succes!"
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
     * Delete Categorie Article
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $this->auth();
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>404,
                'message'=>'L\'Id de la catégorie n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
            return;
        } else {
            $sous_categorie= [
                'scat_id'=>$id
            ];
            $outpout = $this->SousCategorieModel->delete($sous_categorie);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"Catégorie Article supprimée avec succes!"
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