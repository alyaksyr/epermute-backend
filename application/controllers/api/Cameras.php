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
class Cameras extends MY_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('camera_model','CameraModel');

    }

    /**
     * Get Camera 
     * @method: GET
     */
    public function index_get($param='')
    {
        $camera= array();
        $msg ='';

        if (empty($param)) {
            
            foreach ($this->CameraModel->all_camera() as $row)
            {
                $data = $row;
                $camera[] = $data;  
            }     
            if (empty($camera)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $this->set_response($camera, REST_Controller::HTTP_OK);
                $msg='Liste des caméras récupérées avec succès !';
            }
            
        } else {
            $row = $this->CameraModel->camera($param);

            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $camera = $row;
                $this->set_response($camera, REST_Controller::HTTP_OK);
                $msg='Caméra récupérée avec succès !';
            }

        }
        $this->set_response(['status'=>200,'message'=>$msg,'data'=>$camera], REST_Controller::HTTP_OK);
    }

    /**
     * Create New pays
     * @method: POST
     */
    public function index_post()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('camera_code', 'Code', 'trim|required|is_unique[aqi_pp_camera.camera_code]',
            array('is_unique'=>'Ce code de pays existe déja !')
        );
        $this->form_validation->set_rules('camera_resolution', 'Resolution', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{

            $pays = $this->input->post();
            $id = $this->CameraModel->create($pays);
            
            if ($id>0 AND !empty($id)) {
               
                $message = [
                    'status'=>201,
                    'message'=>"Camera ajoutée avec succes!",
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
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('camera_id', 'Camera ID', 'trim|required|numeric');
        $this->form_validation->set_rules('camera_code', 'Code', 'trim|required');
        $this->form_validation->set_rules('camera_resolution', 'Resolution', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $camera = $this->input->post();
            $camera['camera_id'] = $this->input->post('camera_id',TRUE);

            $outpout = $this->CameraModel->update($camera);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Camera Modifiée avec succes!"
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
                'message'=>'L\'Id du type de camera n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $camera= [
                'camera_id'=>$id
            ];
            $outpout = $this->CameraModel->delete($camera);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"Camera supprimée avec succes!"
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