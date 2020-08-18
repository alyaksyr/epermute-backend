<?php
//use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

use Phoneplus\Libraries\REST_Controller;
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
class Articles extends MY_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

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
            
            $article = $this->ArticleModel->all_article();   
            if (empty($article)) {
                $this->set_response([
                    'status'=>false,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $this->set_response($article, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->ArticleModel->article($param);            

            if (empty($row)) {
                $this->set_response([
                    'status'=>400,
                    'message'=>$this->msg_not_found,
                    'data'=>$row
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $article = $row;
            }

        }
        $this->set_response(['status'=>200,'message'=>'Requête exécutée avec succès!','data'=>$article], REST_Controller::HTTP_OK);
    }

    /**
     * Get article
     * @method: GET
     * @param: {Id}
     */
    public function list_article_get($param='')
    {
        $article = array();

        if (empty($param)) {
            foreach ($this->ArticleModel->get_article() as $row)
            {
                $row->caracteristiques = $this->ArticleMetaModel->meta_by_article_id($row->id);
                $article[]= $row;
            }
            if (empty($article)) {
                $this->set_response([
                    'status'=>false,
                    'message'=> $this->msg_not_found
                ],REST_Controller::HTTP_NOT_FOUND);
                return;
            } else {
                $this->set_response($article, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->ArticleModel->get_article($param);            

            if (empty($row)) {
                $this->set_response([
                    'status'=>400,
                    'message'=>$this->msg_not_found,
                    'data'=>$row
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $row->caracteristiques = $this->ArticleMetaModel->meta_by_article_id($row->id);
                $article = $row;
            }

        }
        $this->set_response(['status'=>200,'message'=>'Requête exécutée avec succès!','data'=>$article], REST_Controller::HTTP_OK);
    }

    /**
     * Create New article
     * @method: POST
     */
    public function index_post()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('libelle', 'Libellé', 'trim|required');
        $this->form_validation->set_rules('code', 'Article Code', 'trim|required|is_unique[aqi_pp_article.code]',
            array('is_unique'=>'Ce code existe déja !')
        );

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $data = $this->input->post();
            $data['updated_at'] = date('Y-m-d\TH:i:s.u');
            $data['created_at'] = date('Y-m-d\TH:i:s.u');

            $outpout = $this->ArticleModel->create($data);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Article ajoutée avec succes!",
                    'response'=>$outpout
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
     * Delete article
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $this->auth();
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>404,
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
                    'status'=>200,
                    'message'=>"Article supprimée avec succes!"
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

    /**
     * Update article
     * @method: PUT
     */

    public function index_put()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id', 'article ID', 'trim|required|numeric');
        $this->form_validation->set_rules('libelle', 'Libellé', 'trim|required');
        $this->form_validation->set_rules('code', 'Article Code', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $data = $this->input->post();
            $data['id'] = $this->input->post('id',TRUE);
            $data['updated_at'] = date('Y-m-d\TH:i:s.u');

            $outpout = $this->ArticleModel->update($data);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Article Modifiée avec succes!"
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

}