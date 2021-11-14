<?php defined('BASEPATH') OR exit('No direct script access allowed');

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
class Fiches extends MY_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        // $this->auth();
        parent::__construct();
        $this->load->model('fiche_model','FicheModel');
        $this->load->model('demande_model','DemandeModel');
        $this->load->model('user_model','UserModel');
        $this->load->model('inspection_model','InspectionModel');
        $this->load->model('direction_model','DirectionModel');

    }

    /**
    * Get fiches
    * @method: GET
    * @param: {Id}
    */
    public function index_get($param='')
    {
        $fiche = array();
        $msg='';

        if (empty($param)) {
            
            $fiche = $this->FicheModel->all_fiche();
     
            if (empty($fiche)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $this->set_response($fiche, REST_Controller::HTTP_OK);
                $msg = 'Liste des fiches récupérée avec succès !';
            }
            
        } else {
            $row = $this->FicheModel->fiche($param);            

            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $fiche = $row;
                $this->set_response($fiche, REST_Controller::HTTP_OK);
                $msg = 'fiche récupéré avec succès !';
            }

        }
        $this->set_response(['status'=>200, 'message'=>$msg, 'data'=>$fiche], REST_Controller::HTTP_OK);
    }

    /**
    * Liste fiche  pour le site
    * @method: GET
    */
    public function list_fiches_get($param)
    {
        $fiche = array();
        $msg='';
        /**Verifie si le parametres n'existe pas */
        if (!empty($param)) {
            foreach ($this->FicheModel->all_fiche_by_user($param) as $row)
            {
                $row->demandeur = $this->UserModel->user_information($row->demandeur);
                $row->demandeur->inspection = $this->InspectionModel->inspection_dren($row->demandeur->inspection);
                $row->demandeur->inspection->dren = $this->DirectionModel->direction_detail($row->demandeur->inspection->dren);
                $row->recepteur = $this->UserModel->user_information($row->recepteur);
                $row->recepteur->inspection = $this->InspectionModel->inspection_dren($row->recepteur->inspection);
                $row->recepteur->inspection->dren = $this->DirectionModel->direction_detail($row->recepteur->inspection->dren);

                $fiche[]= $row;
            }
            if (empty($fiche)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $this->set_response($fiche, REST_Controller::HTTP_OK);
                $msg = 'Liste des fiches récupérée avec succès !';
            }
        }else{
           
            $this->set_response([
                'status'=>404,
                'message'=>$this->msg_not_found
            ],
                REST_Controller::HTTP_NOT_FOUND
            );
            return;
        }
        
        $this->set_response(['status'=>200, 'message'=>$msg, 'data'=>$fiche], REST_Controller::HTTP_OK);
    }

    /**
     * Create New fiche
     * @method: POST
     */
    public function index_post()
    {
        // $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id_demandeur', 'Le demandeur', 'trim|required|numeric');
        $this->form_validation->set_rules('id_recepteur', 'Le recepteur', 'trim|required|numeric');
        $this->form_validation->set_rules('num_demande', 'Numéro de la demande', 'trim|required|is_unique[gp5das_fiche.num_demande]',
            array('is_unique'=>'Cette demande n\'est plus active !')
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
            $photo = $this->upload_image($this->input->post('images'));
            $data['fiche_main_image'] = $photo;
            $data['fiche_code'] = $date->getTimestamp();
            $data['fiche_modified'] = date('Y-m-d\TH:i:s.u');
            $data['fiche_added'] = date('Y-m-d\TH:i:s.u');

            $outpout = $this->ficheModel->create($data);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"fiche ajoutée avec succes!",
                    'response'=>base_url().'/'.$outpout
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
     * Delete fiche
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $this->auth();
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>404,
                'message'=>'L\'Id de la fiche n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
            return;
        } else {
            $fiche= [
                'fiche_id'=>$id
            ];
            $outpout = $this->ficheModel->delete($fiche);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"fiche supprimée avec succes!"
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
     * Update fiche
     * @method: PUT
     */

    public function index_put(){
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('fiche_id', 'fiche ID', 'trim|required|numeric');
        $this->form_validation->set_rules('fiche_prix', 'Prix fiche', 'trim|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }else{
            $data = $this->input->post();
            $photo = $this->upload_images($this->input->post('images'),'1595258896','phoneplus');
            $data['fiche_images'] = $photo;
            $data['fiche_prix'] = $this->input->post('prix',TRUE);
            $data['fiche_id'] = $this->input->post('id',TRUE);
            $data['fiche_modified'] = date('Y-m-d\TH:i:s.u');

            $outpout = $this->ficheModel->update($data);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"fiche Modifiée avec succes!"
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
     * Formatage des données de l'article à afficher
     */
    public function get_detail_article($id){
        $article = array();
        $row = $this->ArticleModel->get_article($id);
        $row->marque = $this->MarqueModel->marque_libelle($row->marque);
        $row->categorie = $this->CategorieModel->categorie_libelle($row->categorie);
        $row->sous_categorie = $this->SousCategorieModel->sous_categorie_site($row->sous_categorie);
        $caracteristiques = $this->ArticleMetaModel->meta_by_article_id($row->id);
        $article = $row;
        $article->caracteristiques = $caracteristiques;
        return $article;

    }

    public function split_image($image){
        if(!empty($image) && strlen($image)>2){
            $image = substr($image, 1, -1);
            return explode(",",$image);
        }else{
            return $image;
        }
    }

    public function upload_image($files, $code, $subdir){
        $uploaddir = 'assets/images/fiches/'+$subdir+'/'+$code;
        if(!is_dir($uploaddir)){
            mkdir($uploaddir,0775,true);
        }
        if ($files) {

            $pathinfo = $files['filename'];
            $value = $files['value'];
            $value = substr($descr[0],9);
            $value = str_replace(' ', '+', $value);
            $image = base64_decode($value);
            $ext = pathinfo($pathinfo, PATHINFO_EXTENSION);
            $file_name = 'main';
            $path = $uploaddir.$file_name.'.'.$ext;
            file_put_contents($path, $image);

            return $path;

            // $images[]=$path;
            // $pathinfo = $files['filename'];
            // $image_value = $files['value'];
            // $descr = explode(',', $image_value);
            // $toreplace = $descr[0].',';
            // $value = $descr[1];
			// $value = str_replace(' ', '+', $value);
            // $ext = pathinfo($pathinfo, PATHINFO_EXTENSION);
            // $image = base64_decode($value);
            // $file_name = 'main';
            // $image_name = $file_name.'.'.$ext;
            // $path = $uploaddir.$image_name;
            // file_put_contents($path,$image);
            // return $uploaddir.$image_name;
        } else {
            return ;
        }
        
    }

    public function upload_images($files, $code, $subdir){
        $images = '[';
        $uploaddir = 'assets/images/fiches/'.$subdir.'/'.$code.'/';
        if(!is_dir($uploaddir)){
            mkdir($uploaddir,0775,true);
        }
        if ($files && is_array($files)) {
            $i = 1;
            foreach ($files as $key) {
                $pathinfo = $key['filename'];
                $value = $key['value'];
                $descr = explode(',', $value);
                $value = substr($descr[0],9);
                $value = str_replace(' ', '+', $value);
                $image = base64_decode($value);
                $ext = pathinfo($pathinfo, PATHINFO_EXTENSION);
                $file_name = 'slides_'.($i++);
                $path = $uploaddir.$file_name.'.'.$ext;
                file_put_contents($path, $image);
                $images.=$path.',';

            }
            $images = substr($images,0,-1);
            return $images.']';

        } else {
            return;
        }
        
    }


}