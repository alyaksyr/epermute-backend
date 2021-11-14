<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Epermute\Libraries\REST_Controller;
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
class Comments extends MY_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('comment_model','CommentModel');

    }

    /**
     * Get Comments 
     * @method: GET
     */
    public function index_get($param='')
    {
        $comment= array();
        $msg ='';

        if (empty($param)) {
            
            foreach ($this->CommentModel->all_comment() as $row)
            {
                $data = $row;
                $comment[] = $data;  
            }     
            if (empty($comment)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $this->set_response($comment, REST_Controller::HTTP_OK);
                $msg='Liste des commentaires récupérés avec succès !';
            }
            
        } else {
            $row = $this->CommentModel->comment($param);

            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $comment = $row;
                $this->set_response($comment, REST_Controller::HTTP_OK);
                $msg='Commentaire récupéré avec succès !';
            }

        }
        $this->set_response(['status'=>200,'message'=>$msg,'data'=>$comment], REST_Controller::HTTP_OK);
    }

    /**
     * Create New pays
     * @method: POST
     */
    public function index_post()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);
        $this->form_validation->set_rules('cmt_author_name', 'Nom de l\'auteur', 'trim|required');
        $this->form_validation->set_rules('cmt_author_email', 'Email de l\'auteur', 'trim|required');
        $this->form_validation->set_rules('cmt_rate', 'La note attribuée', 'trim|required|numeric');
        $this->form_validation->set_rules('cmt_id_product', 'L\'id du produit', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $data = $this->input->post();
            $data['cmt_created_at'] = date('Y-m-d\TH:i:s.u');
            $data['cmt_updated_at'] = date('Y-m-d\TH:i:s.u');
            $data['cmt_status'] = 1;
            $data['cmt_checked'] = 0;
            $id = $this->CommentModel->create($data);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>201,
                    'message'=>"Commentaire ajouté avec succes!",
                    'response'=>base_url().'comments/'.$id
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
     * Update commentaire
     * @method: PUT
     */
    public function index_put()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('cmt_id', 'Commentaire ID', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $comment = $this->input->post();
            $comment['cmt_id'] = $this->input->post('cmt_id',TRUE);
            $comment['cmt_updated_at'] = date('Y-m-d\TH:i:s.u');

            $outpout = $this->CommentModel->update($comment);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Comment Modifié avec succes!"
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
        $this->auth();
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>404,
                'message'=>'L\'Id du commentaire n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $comment= [
                'cmt_id'=>$id
            ];
            $outpout = $this->CommentModel->delete($comment);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"Commentaire supprimé avec succes!"
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