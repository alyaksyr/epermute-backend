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
class Boutiques extends MY_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('boutique_model','BoutiqueModel');
        $this->load->model('user_model','UserModel');

    }

    /**
     * Get Boutique
     * @method: GET
     * @param: {Id}
     */
    public function index_get($param='')
    {
        $boutique= array();
        $msg ='';
        $this->load->model('pays_model','PaysModel');
        $this->load->model('ville_model','VilleModel');

        if (empty($param)) {
            
            foreach ($this->BoutiqueModel->all_boutique() as $row)
            {
                $data['id'] = (int)$row['id'];
                $data['code'] = $row['boutique_ID'];
                $data['modified'] = $row['modified'];
                $data['created'] = $row['created'];
                $data['mobile'] = $row['mobile'];
                $data['quartier'] = $row['quartier'];
                $data['telephone'] = $row['telephone'];
                $data['nom'] = $row['raison_social'];
                $data['pays'] = $this->PaysModel->pays($row['id_pays']);
                $data['ville'] = $this->VilleModel->ville($row['id_ville']);
                $data['boite_postale'] = $row['boite_postale'];
                $data['situation'] = $row['situation'];
                $data['sigle'] = $row['sigle'];
                $data['logo'] = $row['logo'];
                $data['site_web'] = $row['site_web'];
                $data['email'] = $row['email'];
                $data['latitude'] = $row['latitude'];
                $data['longitude'] = $row['longitude'];
                $data['description'] = $row['description'];
                $data['status'] = (int)$row['status'];
                $data['proprietaire'] = $this->UserModel->user_detail($row['id_admin']);
                $boutique[] = $data;  
            }     
            if (empty($boutique)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $msg = 'Liste des boutiques recuperée avec succès !';
                $this->set_response($boutique, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->BoutiqueModel->boutique($param);

            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $boutique['id'] = (int)$row->id;
                $boutique['code'] = $row->boutique_ID;
                $boutique['modified'] = $row->modified;
                $boutique['created'] = $row->created;
                $boutique['mobile'] = $row->mobile;
                $boutique['quartier'] = $row->quartier;
                $boutique['telephone'] = $row->telephone;
                $boutique['nom'] = $row->raison_social;
                $boutique['pays'] = $this->PaysModel->pays($row->id_pays);
                $boutique['ville'] = $this->VilleModel->ville($row->id_ville);
                $boutique['boite_postale'] = $row->boite_postale;
                $boutique['situation'] = $row->situation;
                $boutique['sigle'] = $row->sigle;
                $boutique['logo'] = $row->logo;
                $boutique['site_web'] = $row->site_web;
                $boutique['email'] = $row->email;
                $boutique['latitude'] = $row->latitude;
                $boutique['longitude'] = $row->longitude;
                $boutique['description'] = $row->description;
                $boutique['status'] = (int)$row->status;
                $boutique['proprietaire'] = $this->UserModel->user_detail($row->id_admin);

                $this->set_response($boutique, REST_Controller::HTTP_OK);

                $msg = 'Boutique recuperée avec succès !';
            }

        }
        $this->set_response(['status'=>200,'message'=>$msg,'data'=>$boutique], REST_Controller::HTTP_OK);
    }

    /**
     * Create New Boutique
     * @method: POST
     */
    public function index_post()
    {
        $min = 0;
        $max = 9;
        $nombre='';
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('raison_social', 'RaisonSocial', 'trim|required');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $data = $this->input->post();
            $data['modified'] = date('Y-m-d\TH:i:s.u');
            $data['created'] = date('Y-m-d\TH:i:s.u');
            for($i=0; $i<7;$i++){
                $nombre.=rand(0,9);
            }
            $data['boutique_ID'] = $nombre;
            $outpout = $this->BoutiqueModel->create($data);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Boutique ajoutée avec succes!",
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
     * Delete Boutique
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>404,
                'message'=>'L\'Id de la boutique n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $boutique= [
                'id'=>$id
            ];
            $outpout = $this->BoutiqueModel->delete($boutique);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"Boutique supprimée avec succes!"
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
     * Update Boutique
     * @method: PUT
     */

    public function index_put(){
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id', 'Boutique ID', 'trim|required|numeric');
        $this->form_validation->set_rules('raison_social', 'Raison Sociale', 'trim|required');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

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
            $data['modified'] = date('Y-m-d\TH:i:s.u');

            $outpout = $this->BoutiqueModel->update($data);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"Boutique Modifiée avec succes!"
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