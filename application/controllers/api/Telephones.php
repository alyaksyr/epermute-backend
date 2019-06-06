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
class Telephones extends REST_Controller {

    protected $telephone_table = 'aqi_pp_telephone';
    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('telephone_model');
        $this->load->model('article_model');

    }

    /**
     * Get all telephones or one
     * @method: GET
     * params: {id or code or empty}
     */

    public function index_get($param='')
    {
        $data = array();

        if (empty($param)) {

            $data = $this->set_data($this->telephone_model->get_telephones());
            
            if (empty($data)) {
                $this->set_response([
                    'status'=>false,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($data, REST_Controller::HTTP_OK);
            }
            
        } else {
            $data = $this->set_data($this->telephone_model->get_telephone($param));

            if (empty($data)) {
                $this->set_response([
                    'status'=>false,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($data, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response($data, REST_Controller::HTTP_OK);
    }
    /**
     * Create a telephone
     * @method: POST
     */

    public function index_post()
    {
        $_POST = json_decode(file_get_contents('php://input'),true);
        $data = $this->input->post();
        $resp = $this->telephone_model->create_telephone($data);
        if ($resp['code']=0) {
            $this->set_response([
                'status'=>true,
                'message'=>$resp['msg']
            ],
                REST_Controller::HTTP_CREATED
            );
        } else {
            $this->set_response([
                'status'=>false,
                'message'=>$resp['msg']
            ],
            REST_Controller::HTTP_BAD_REQUEST
            );
        }
        
    }

    /**
     * Update a telephone
     * @method: POST
     */

    public function index_put()
    {

        $_POST = json_decode(file_get_contents('php://input'),true);
        $data = $this->put();
        $id = $data['id_article'];
        $this->db->update($this->telephone_table, $data, array('id_article'=>$id));

        $this->set_response(['telephone modifiée avec succès.'], REST_Controller::HTTP_CREATED);
    }

    public function index_delete($id)
    {
        $this->db->delete($this->telephone_table, array('id'=>$id));

        $this->set_response(['telephone supprimée avec succès.'], REST_Controller::HTTP_OK);
    }

    public function valid_code($code)
    {
        $qry = $this->db->get_where($this->telephone_table, array('code'=>$code));
        return ($qry->num_rows() <= 0)? true: false;              
    }

    public function set_data($args=array()){
        $data = array();
        foreach ($args as $row) {
            $telephone['id'] = $row['id_article'];
            $telephone['article'] = $this->article_model->get_article($row['id_article']);
            $telephone['categorie'] = $this->telephone_model->get_categorie_telephone($row['id_categorie']);
            $telephone['marque'] = $this->telephone_model->get_marque($row['id_marque']);
            $telephone['modele'] = $this->telephone_model->get_modele($row['id_modele']);
            $telephone['memoire'] = $this->telephone_model->get_memoire($row['memoire']);
            $telephone['reseau'] = $this->telephone_model->get_reseau($row['id_reseau']);
            $telephone['camera'] = $this->telephone_model->get_camera($row['camera']);
            $telephone['ecran'] = $this->telephone_model->get_ecran($row['id_ecran']);
            $telephone['systeme'] = $this->telephone_model->get_systeme($row['id_systeme']);
            $telephone['batterie'] = $row['batterie'];
            $telephone['sim'] = $row['carte_sim'];
            $telephone['empriente'] = $row['empriente_digitale'];
            $telephone['faciale'] = $row['deverrouillage_faciale'];
            $telephone['processeur'] = $row['processeur'];
            $telephone['connectivite'] = $row['connectivite'];
            $telephone['resolution'] = $row['resolution'];
            $telephone['dimension'] = $row['dimension'];
            $telephone['infos'] = $row['infos'];
            
            $data[]= $telephone;
        }
        return $data;
    } 

}