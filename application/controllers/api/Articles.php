<?php
//use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

use Phoneplus\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
require_once APPPATH . 'libraries/JWT.php';
require APPPATH . 'libraries/Format.php';

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
class Articles extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('article_model','ArticleModel');
        $this->load->model('sousCategorie_model','SousCategorieModel');
        $this->load->model('Categorie_model','CategorieModel');
        $this->load->model('articleMeta_model','ArticleMetaModel');
        $this->load->model('marque_model','MarqueModel');

    }

    /**
     * Get article
     * @method: GET
     * @param: {Id}
     */
    public function index_get($param='')
    {
        $article = array();

        if (empty($param)) {
            
            foreach ($this->ArticleModel->all_article() as $row)
            {
                $data['id'] = $row->id;
                $data['code'] = $row->code;
                $data['created'] = $row->created_at;
                $data['updated'] = $row->updated_at;
                $data['libelle'] = $row->libelle;
                $data['status'] = $row->status;
                $data['qte_package'] = $row->qte_package;
                $data['marque'] = $this->get_marque($row->id_marque);
                $data['fabricant'] = $row->fabricant;
                $data['categorie'] = $this->get_categorie($row->id_sous_categorie);
                $data['caracteristiques'] = $this->ArticleMetaModel->meta_by_article_id($row->id);

                $article[] = $data;  
            }     
            if (empty($article)) {
                $this->set_response([
                    'status'=>false,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($article, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->ArticleModel->article($param);
            $article['id'] = $row->id;
            $article['code'] = $row->code;
            $article['created'] = $row->created_at;
            $article['updated'] = $row->updated_at;
            $article['libelle'] = $row->libelle;
            $article['status'] = $row->status;
            $article['qte_package'] = $row->qte_package;
            $article['marque'] = $this->get_marque($row->id_marque);
            $article['fabricant'] = $row->fabricant;
            $article['categorie'] = $this->get_categorie($row->id_sous_categorie);
            $article['caracteristiques'] = $this->ArticleMetaModel->meta_by_article_id($row->id);
            

            if (empty($article)) {
                $this->set_response([
                    'status'=>false,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($article, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response($article, REST_Controller::HTTP_OK);
    }

    /**
     * Create New article
     * @method: POST
     */
    public function index_post()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('raison_social', 'RaisonSocial', 'trim|required');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>false,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $data = $this->input->post();
            $data['modified'] = date('Y-m-d\TH:i:s.u');
            $data['created'] = date('Y-m-d\TH:i:s.u');
            $data['code'] = time();

            $outpout = $this->ArticleModel->create($data);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"article ajoutée avec succes!"
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
     * Delete article
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>FALSE,
                'message'=>'L\'Id de la article n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $article= [
                'id'=>$id
            ];
            $outpout = $this->ArticleModel->delete($article);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"article supprimée avec succes!"
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

    /**
     * Update article
     * @method: PUT
     */

    public function index_put(){
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id', 'article ID', 'trim|required|numeric');
        $this->form_validation->set_rules('raison_social', 'RaisonSocial', 'trim|required');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>false,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $data = $this->input->post();
            $data['id'] = $this->input->post('id',TRUE);
            $data['modified'] = date('Y-m-d\TH:i:s.u');

            $outpout = $this->ArticleModel->update($data);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"article Modifiée avec succes!"
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

    public function get_categorie($id){
        $row = $this->SousCategorieModel->sous_categorie($id);
        $categorie['id'] = $row->id;
        $categorie['libelle'] = $row->libelle;
        $categorie['type'] = $this->CategorieModel->categorie_libelle($row->id_categorie);

        return $categorie;
    }

    public function get_marque($id){
        $row = $this->MarqueModel->marque_libelle($id);
        $marque['libelle'] =  $row->libelle;
        return $marque;
    }
}