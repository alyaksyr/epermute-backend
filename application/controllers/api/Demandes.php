<?php
//use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

use Epermute\Libraries\REST_Controller;
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
class Demandes extends MY_Controller {
    protected $msg_not_found= 'Aucun n\'enregisterment trouvé !';
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('demande_model','DemandeModel');
        $this->load->model('personnel_model','PersonnelModel');
        $this->load->model('inspection_model','InspectionModel');
        $this->load->model('direction_model','DirectionModel');

    }

    /**
     * Get demande
     * @method: GET
     * @param: {Id}
     */
    public function index_get($param='')
    {
        $demande= array();
        $msg ='';
        $this->load->model('inspection_model','InspectionModel');
        $this->load->model('direction_model','DirectionModel');

        if (empty($param)) {
            
            $demande = $this->DemandeModel->all_demande();
            if (empty($demande)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $msg = 'Liste des demandes recuperée avec succès !';
                $this->set_response($demande, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->DemandeModel->demande($param);

            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {

                $demande = $row;
                $this->set_response($demande, REST_Controller::HTTP_OK);

                $msg = 'demande recuperée avec succès !';
            }

        }
        $this->set_response(['status'=>200,'message'=>$msg,'data'=>$demande], REST_Controller::HTTP_OK);
    }

    /**
     * Get all infos de la demande
     * @method: GET
     * @param: {Id}
     */
    public function list_demande_get($param='')
    {
        $demande= array();
        $msg ='';

        if (empty($param)) {
            foreach($this->DemandeModel->get_demande() as $row){
                
                $row->inspection = $this->InspectionModel->inspection_dren($row->inspection);
                $row->inspection->dren = $this->DirectionModel->direction_detail($row->inspection->dren);
                $row->demandeur = $this->PersonnelModel->personnel_information($row->demandeur);
                $row->demandeur->inspection = $this->InspectionModel->inspection_dren($row->demandeur->inspection);
                $row->demandeur->inspection->dren = $this->DirectionModel->direction_detail($row->demandeur->inspection->dren);

                $demande[] = $row;
            }
            if (empty($demande)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $msg = 'Liste des demandes recuperée avec succès !';
                $this->set_response($demande, REST_Controller::HTTP_OK);
            }
            
        } else {
           $row = $this->DemandeModel->get_demande($param);

            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $row->inspection = $this->InspectionModel->inspection_dren($row->inspection);
                $row->inspection->dren = $this->DirectionModel->direction_detail($row->inspection->dren);
                $row->demandeur = $this->PersonnelModel->personnel($row->demandeur);
                $row->demandeur->inspection = $this->InspectionModel->inspection_dren($row->demandeur->inspection);
                $row->demandeur->inspection->dren = $this->DirectionModel->direction_detail($row->demandeur->inspection->dren);

                $demande = $row;
                $this->set_response($demande, REST_Controller::HTTP_OK);

                $msg = 'demande recuperée avec succès !';
            }

        }
        $this->set_response(['status'=>200,'message'=>$msg,'data'=>$demande], REST_Controller::HTTP_OK);
    }

    /**
     * Create New demande
     * @method: POST
     */
    public function index_post()
    {
        $this->auth();
        $min = 0;
        $max = 9;
        $nombre='';
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id_demandeur', 'Demandeur', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $data = $this->input->post();
            $data['created_at'] = date('Y-m-d\TH:i:s.u');
            $data['updated_at'] = date('Y-m-d\TH:i:s.u');
            for($i=0; $i<7;$i++){
                $nombre.=rand(0,9);
            }
            $rand = substr(uniqid('',true),-5);
            $data['num_demande'] = $nombre .' '. strtoupper($rand);
            $outpout = $this->DemandeModel->create($data);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"demande ajoutée avec succes!",
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
     * Delete demande
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $this->auth();
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>404,
                'message'=>'L\'Id de la demande n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $demande= [
                'id'=>$id
            ];
            $outpout = $this->DemandeModel->delete($demande);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"demande supprimée avec succes!"
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
     * Update demande
     * @method: PUT
     */

    public function index_put()
    {
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id', 'demande ID', 'trim|required|numeric');
        $this->form_validation->set_rules('num_demande', 'Numéro de la demande', 'trim|required');

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

            $outpout = $this->DemandeModel->update($data);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"demande Modifiée avec succes!"
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